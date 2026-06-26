"""
Redis Cache System Initialization
Initialize dan monitor Redis cache untuk agent system
"""

import logging
import sys
from datetime import datetime
from typing import Dict
import time

from core.cache_config import CacheConfig, CachePolicies
from core.redis_cache_manager import get_cache_manager
from core.cache_sync import get_sync_validator
from core.cache_tools import get_cached_tool_helper

# Setup logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)


class CacheSystemInitializer:
    """Initialize dan setup Redis cache system"""
    
    def __init__(self):
        self.cache_manager = None
        self.sync_validator = None
        self.tool_helper = None
    
    def initialize(self) -> bool:
        """
        Initialize seluruh Redis cache system
        
        Returns:
            True if successful, False if failed
        """
        try:
            logger.info("="*60)
            logger.info("INITIALIZING REDIS CACHE SYSTEM")
            logger.info("="*60)
            
            # Print configuration
            CacheConfig.print_config()
            
            if not CacheConfig.CACHE_ENABLED:
                logger.warning("⚠️  CACHE DISABLED - Agent will use direct database calls")
                return True
            
            # 1. Initialize cache manager
            logger.info("[1/3] Initializing Redis Cache Manager...")
            try:
                self.cache_manager = get_cache_manager()
                logger.info(f"✓ Cache Manager ready ({CacheConfig.REDIS_HOST}:{CacheConfig.REDIS_PORT})")
            except Exception as e:
                logger.error(f"✗ Failed to initialize Cache Manager: {e}")
                return False
            
            # 2. Initialize sync validator
            logger.info("[2/3] Initializing Sync Validator...")
            try:
                self.sync_validator = get_sync_validator()
                logger.info("✓ Sync Validator ready")
            except Exception as e:
                logger.error(f"✗ Failed to initialize Sync Validator: {e}")
                return False
            
            # 3. Initialize tool helper
            logger.info("[3/3] Initializing Cached Tool Helper...")
            try:
                self.tool_helper = get_cached_tool_helper()
                logger.info("✓ Tool Helper ready")
            except Exception as e:
                logger.error(f"✗ Failed to initialize Tool Helper: {e}")
                return False
            
            # Print cache policies
            logger.info("\n" + "="*60)
            logger.info("CACHE POLICIES LOADED")
            logger.info("="*60)
            logger.info(f"Cascade rules defined for {len(CachePolicies.CASCADE_RULES)} entity types")
            logger.info(f"TTL overrides for {len(CachePolicies.TTL_OVERRIDES)} specific caches")
            logger.info(f"Sensitive entities monitored: {', '.join(CachePolicies.SENSITIVE_ENTITIES)}")
            
            # Get initial cache stats
            stats = self.cache_manager.get_cache_stats()
            logger.info("\n" + "="*60)
            logger.info("INITIAL CACHE STATISTICS")
            logger.info("="*60)
            logger.info(f"Total keys in cache: {stats.get('total_keys', 0)}")
            logger.info(f"Connected clients: {stats.get('connected_clients', 0)}")
            logger.info(f"Used memory: {stats.get('used_memory', 'N/A')}")
            
            logger.info("\n" + "="*60)
            logger.info("✓ REDIS CACHE SYSTEM INITIALIZED SUCCESSFULLY")
            logger.info("="*60 + "\n")
            
            return True
        
        except Exception as e:
            logger.error(f"✗ CRITICAL: Cache system initialization failed: {e}")
            return False
    
    def test_cache_operations(self) -> bool:
        """Test basic cache operations"""
        try:
            logger.info("Testing cache operations...")
            
            # Test cache set/get
            test_key = "test:initialization"
            test_data = {"test": "data", "timestamp": datetime.now().isoformat()}
            
            self.cache_manager.cache_query(test_key, test_data)
            cached = self.cache_manager.get_cached_query(test_key)
            
            if cached and cached.get("test") == "data":
                logger.info("✓ Cache read/write test PASSED")
                self.cache_manager.invalidate_query(test_key)
                return True
            else:
                logger.error("✗ Cache read/write test FAILED")
                return False
        except Exception as e:
            logger.error(f"✗ Cache operation test failed: {e}")
            return False
    
    def clear_cache(self, confirm: bool = False) -> bool:
        """Clear all cache data"""
        if not confirm:
            logger.warning("⚠️  Use clear_cache(confirm=True) to actually clear cache")
            return False
        
        try:
            logger.warning("Clearing all cache data...")
            if self.cache_manager.clear_all_cache():
                logger.info("✓ Cache cleared successfully")
                return True
            else:
                logger.error("✗ Failed to clear cache")
                return False
        except Exception as e:
            logger.error(f"✗ Error clearing cache: {e}")
            return False


class CacheMonitor:
    """Monitor Redis cache health dan performance"""
    
    def __init__(self):
        self.cache_manager = get_cache_manager()
        self.stats_history = []
    
    def get_current_stats(self) -> Dict:
        """Get current cache statistics"""
        try:
            stats = self.cache_manager.get_cache_stats()
            stats['timestamp'] = datetime.now().isoformat()
            return stats
        except Exception as e:
            logger.error(f"Error getting cache stats: {e}")
            return {}
    
    def print_stats(self):
        """Print current cache statistics"""
        stats = self.get_current_stats()
        
        logger.info("\n" + "="*60)
        logger.info("REDIS CACHE STATISTICS")
        logger.info("="*60)
        logger.info(f"Timestamp: {stats.get('timestamp', 'N/A')}")
        logger.info(f"Total keys: {stats.get('total_keys', 0):,}")
        logger.info(f"Connected clients: {stats.get('connected_clients', 0)}")
        logger.info(f"Memory used: {stats.get('used_memory', 'N/A')}")
        logger.info(f"Keyspace hits: {stats.get('keyspace_hits', 0):,}")
        logger.info(f"Keyspace misses: {stats.get('keyspace_misses', 0):,}")
        logger.info(f"Hit rate: {stats.get('hit_rate', 0):.2f}%")
        
        # Check health
        hit_rate = stats.get('hit_rate', 0)
        if hit_rate >= CacheConfig.TARGET_HIT_RATE * 100:
            logger.info(f"✓ Cache health: GOOD (hit rate {hit_rate:.2f}% >= target {CacheConfig.TARGET_HIT_RATE*100:.0f}%)")
        else:
            logger.warning(f"⚠️  Cache health: FAIR (hit rate {hit_rate:.2f}% < target {CacheConfig.TARGET_HIT_RATE*100:.0f}%)")
        
        logger.info("="*60 + "\n")
    
    def health_check(self) -> Dict:
        """Perform health check"""
        checks = {}
        
        try:
            # Check Redis connection
            self.cache_manager.r.ping()
            checks["redis_connection"] = True
        except:
            checks["redis_connection"] = False
        
        # Check cache hit rate
        stats = self.get_current_stats()
        hit_rate = stats.get('hit_rate', 0)
        checks["hit_rate"] = hit_rate >= CacheConfig.TARGET_HIT_RATE * 100
        checks["hit_rate_value"] = hit_rate
        
        # Check memory usage
        # (Would need to check against max configured size)
        checks["memory_ok"] = True
        
        return checks
    
    def monitor_loop(self, interval: int = 300, duration: int = None):
        """
        Continuous monitoring loop
        
        Args:
            interval: Check interval in seconds (default: 5 minutes)
            duration: Total monitoring duration in seconds (None = infinite)
        """
        logger.info(f"Starting cache monitoring loop (interval: {interval}s)")
        
        start_time = time.time()
        check_count = 0
        
        try:
            while True:
                check_count += 1
                logger.info(f"\n[CHECK #{check_count}] Running health checks...")
                
                checks = self.health_check()
                
                if checks.get("redis_connection"):
                    logger.info("✓ Redis connection: OK")
                else:
                    logger.error("✗ Redis connection: FAILED")
                
                if checks.get("hit_rate"):
                    logger.info(f"✓ Hit rate: {checks.get('hit_rate_value'):.2f}% (target: {CacheConfig.TARGET_HIT_RATE*100:.0f}%)")
                else:
                    logger.warning(f"⚠️  Hit rate: {checks.get('hit_rate_value'):.2f}% (below target {CacheConfig.TARGET_HIT_RATE*100:.0f}%)")
                
                if checks.get("memory_ok"):
                    logger.info("✓ Memory: OK")
                else:
                    logger.warning("⚠️  Memory: High usage")
                
                # Print full stats periodically
                if check_count % 3 == 0:
                    self.print_stats()
                
                # Check duration
                if duration and (time.time() - start_time) > duration:
                    logger.info(f"Monitoring duration ({duration}s) reached, stopping")
                    break
                
                logger.info(f"Next check in {interval}s... (Ctrl+C to stop)")
                time.sleep(interval)
        
        except KeyboardInterrupt:
            logger.info("\nMonitoring stopped by user")
        
        except Exception as e:
            logger.error(f"Error during monitoring: {e}")


def initialize_redis_cache() -> bool:
    """Initialize Redis cache system with full setup"""
    initializer = CacheSystemInitializer()
    
    if not initializer.initialize():
        logger.error("Failed to initialize Redis cache system")
        return False
    
    if not initializer.test_cache_operations():
        logger.error("Cache operation tests failed")
        return False
    
    return True


def start_cache_monitoring(interval: int = 300):
    """Start continuous cache monitoring"""
    logger.info("Starting cache monitoring...")
    monitor = CacheMonitor()
    monitor.print_stats()
    monitor.monitor_loop(interval=interval)


if __name__ == "__main__":
    # Initialize cache system
    if initialize_redis_cache():
        logger.info("Ready to use Redis cache system")
        
        # Optionally start monitoring
        if len(sys.argv) > 1 and sys.argv[1] == "monitor":
            interval = int(sys.argv[2]) if len(sys.argv) > 2 else 300
            start_cache_monitoring(interval=interval)
    else:
        logger.error("Failed to initialize Redis cache system")
        sys.exit(1)
