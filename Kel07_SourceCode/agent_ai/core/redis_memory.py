"""
Redis-based Chat Context Memory System
Menyimpan chat history dan user preferences di Redis untuk fast access
"""

import redis
import json
import logging
from typing import List, Dict, Optional
from datetime import datetime, timedelta
import os

logger = logging.getLogger(__name__)

class RedisMemoryManager:
    """
    Redis-based memory manager untuk chat context
    Fast access (1-5ms) dengan auto-cleanup via TTL
    """
    
    def __init__(self, host: str = "localhost", port: int = 6379, db: int = 0):
        """
        Initialize Redis connection
        
        Args:
            host: Redis host (default: localhost)
            port: Redis port (default: 6379)
            db: Redis database number (default: 0)
        """
        try:
            self.r = redis.Redis(
                host=host,
                port=port,
                db=db,
                decode_responses=True,
                socket_connect_timeout=5,
                socket_keepalive=True,
                socket_keepalive_options={
                    1: 1,
                    2: 3,
                    3: 5,
                } if hasattr(redis, 'socket_keepalive_options') else None
            )
            # Test connection
            self.r.ping()
            logger.info(f"✓ Redis connected: {host}:{port}/db{db}")
        except Exception as e:
            logger.error(f"✗ Redis connection failed: {e}")
            raise
    
    def load_context(self, user_id: int) -> Dict:
        """
        Load chat context untuk user dari Redis
        
        Returns:
            {
                "messages": [...],
                "preferences": {...},
                "session_state": {...},
                "last_action": "..."
            }
        """
        try:
            context = self.r.get(f"chat_context:{user_id}")
            if context:
                logger.debug(f"[{user_id}] Chat context loaded from Redis")
                return json.loads(context)
            
            logger.debug(f"[{user_id}] Chat context not found, returning empty")
            return {
                "messages": [],
                "preferences": {},
                "session_state": {},
                "last_action": None
            }
        except Exception as e:
            logger.error(f"[{user_id}] Error loading context: {e}")
            return {"messages": [], "preferences": {}}
    
    def save_context(self, user_id: int, context: Dict, ttl: int = 86400) -> bool:
        """
        Save chat context ke Redis dengan TTL
        
        Args:
            user_id: User ID
            context: Context dict
            ttl: Time to live in seconds (default: 24 hours)
        
        Returns:
            True if successful, False otherwise
        """
        try:
            self.r.setex(
                f"chat_context:{user_id}",
                ttl,
                json.dumps(context, ensure_ascii=False)
            )
            logger.debug(f"[{user_id}] Chat context saved to Redis (TTL: {ttl}s)")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error saving context: {e}")
            return False
    
    def add_message(self, user_id: int, role: str, content: str) -> bool:
        """
        Add message ke chat context
        
        Args:
            user_id: User ID
            role: "user" atau "assistant"
            content: Message content
        
        Returns:
            True if successful
        """
        try:
            context = self.load_context(user_id)
            
            context["messages"].append({
                "role": role,
                "content": content,
                "timestamp": datetime.now().isoformat()
            })
            
            # Keep only last 100 messages untuk save memory
            if len(context["messages"]) > 100:
                context["messages"] = context["messages"][-100:]
            
            self.save_context(user_id, context)
            logger.debug(f"[{user_id}] Message added (role={role}, length={len(content)})")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error adding message: {e}")
            return False
    
    def get_recent_messages(self, user_id: int, limit: int = 20) -> List[Dict]:
        """
        Get recent messages (last N messages)
        Gunakan untuk agent context window
        
        Args:
            user_id: User ID
            limit: Berapa pesan yang ingin diambil (default: 20)
        
        Returns:
            List of recent messages
        """
        try:
            context = self.load_context(user_id)
            messages = context.get("messages", [])
            return messages[-limit:] if messages else []
        except Exception as e:
            logger.error(f"[{user_id}] Error getting messages: {e}")
            return []
    
    def set_user_preferences(self, user_id: int, preferences: Dict) -> bool:
        """
        Set user preferences (favorite grouping, group size, etc)
        
        Args:
            user_id: User ID
            preferences: Preferences dict
        
        Returns:
            True if successful
        """
        try:
            context = self.load_context(user_id)
            context["preferences"] = preferences
            self.save_context(user_id, context)
            logger.debug(f"[{user_id}] Preferences updated")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error setting preferences: {e}")
            return False
    
    def get_user_preferences(self, user_id: int) -> Dict:
        """
        Get user preferences
        
        Returns:
            Preferences dict
        """
        try:
            context = self.load_context(user_id)
            return context.get("preferences", {})
        except Exception as e:
            logger.error(f"[{user_id}] Error getting preferences: {e}")
            return {}
    
    def set_session_state(self, user_id: int, state: Dict) -> bool:
        """
        Set session state (prodi_id, kategori_pa, angkatan, etc)
        
        Args:
            user_id: User ID
            state: Session state dict
        
        Returns:
            True if successful
        """
        try:
            context = self.load_context(user_id)
            context["session_state"] = state
            self.save_context(user_id, context)
            logger.debug(f"[{user_id}] Session state updated")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error setting session state: {e}")
            return False
    
    def get_session_state(self, user_id: int) -> Dict:
        """
        Get session state
        
        Returns:
            Session state dict
        """
        try:
            context = self.load_context(user_id)
            return context.get("session_state", {})
        except Exception as e:
            logger.error(f"[{user_id}] Error getting session state: {e}")
            return {}
    
    def set_last_action(self, user_id: int, action: str) -> bool:
        """
        Track last action taken by user
        
        Args:
            user_id: User ID
            action: Action name (e.g., "create_group_by_grades")
        
        Returns:
            True if successful
        """
        try:
            context = self.load_context(user_id)
            context["last_action"] = {
                "name": action,
                "timestamp": datetime.now().isoformat()
            }
            self.save_context(user_id, context)
            logger.debug(f"[{user_id}] Last action tracked: {action}")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error setting last action: {e}")
            return False
    
    def get_last_action(self, user_id: int) -> Optional[Dict]:
        """
        Get last action taken by user
        
        Returns:
            Action dict or None
        """
        try:
            context = self.load_context(user_id)
            return context.get("last_action")
        except Exception as e:
            logger.error(f"[{user_id}] Error getting last action: {e}")
            return None
    
    def clear_context(self, user_id: int) -> bool:
        """
        Clear chat context untuk user
        
        Args:
            user_id: User ID
        
        Returns:
            True if successful
        """
        try:
            self.r.delete(f"chat_context:{user_id}")
            logger.info(f"[{user_id}] Chat context cleared")
            return True
        except Exception as e:
            logger.error(f"[{user_id}] Error clearing context: {e}")
            return False
    
    def clear_all(self) -> bool:
        """
        Clear all chat contexts (USE WITH CAUTION)
        
        Returns:
            True if successful
        """
        try:
            pattern = "chat_context:*"
            keys = self.r.keys(pattern)
            if keys:
                self.r.delete(*keys)
            logger.warning(f"All chat contexts cleared ({len(keys)} entries)")
            return True
        except Exception as e:
            logger.error(f"Error clearing all contexts: {e}")
            return False
    
    def get_stats(self) -> Dict:
        """
        Get Redis memory stats
        
        Returns:
            Stats dict
        """
        try:
            info = self.r.info()
            keys = self.r.keys("chat_context:*")
            
            return {
                "total_keys": len(keys),
                "used_memory": f"{info.get('used_memory_human', 'N/A')}",
                "connected_clients": info.get('connected_clients', 0),
                "uptime": f"{info.get('uptime_in_seconds', 0)}s",
                "operations_per_sec": info.get('instantaneous_ops_per_sec', 0)
            }
        except Exception as e:
            logger.error(f"Error getting stats: {e}")
            return {}


# Global instance
_redis_manager = None

def get_redis_manager(host: str = None, port: int = None) -> RedisMemoryManager:
    """
    Get or create Redis manager singleton
    
    Args:
        host: Redis host (from .env REDIS_HOST)
        port: Redis port (from .env REDIS_PORT)
    
    Returns:
        RedisMemoryManager instance
    """
    global _redis_manager
    
    if _redis_manager is None:
        host = host or os.getenv("REDIS_HOST", "localhost")
        port = port or int(os.getenv("REDIS_PORT", 6379))
        _redis_manager = RedisMemoryManager(host=host, port=port)
    
    return _redis_manager
