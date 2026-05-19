"""
MongoDB Long-term Memory - Setup dan Testing Script

Jalankan script ini untuk:
1. Test MongoDB connection
2. Create collections dan indexes
3. Verify sistem long-term memory siap digunakan
"""

import sys
import os
from pathlib import Path

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent))

from core.mongodb import get_mongodb, test_mongodb_connection
from core.mongo_memory import get_mongo_memory
from datetime import datetime, timedelta
import logging

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(name)s] %(levelname)s: %(message)s'
)
logger = logging.getLogger(__name__)


def test_mongodb_setup():
    """Test dan setup MongoDB untuk long-term memory"""
    
    print("\n" + "="*60)
    print("🗄️  MONGODB LONG-TERM MEMORY SETUP TEST")
    print("="*60 + "\n")
    
    # Test connection
    print("[1/5] Testing MongoDB connection...")
    success = test_mongodb_connection()
    if not success:
        print("[✗] MongoDB connection failed. Please check:")
        print("  - MongoDB is running (mongod service)")
        print("  - Host: localhost, Port: 27017")
        print("  - Database: VokasiTeraDB")
        print("  - Check .env for MONGO_HOST, MONGO_PORT, MONGO_DB")
        return False
    print("[✓] MongoDB connected successfully\n")
    
    # Get MongoDB instance
    mongo = get_mongodb()
    if not mongo.is_connected():
        print("[✗] Failed to initialize MongoDB connection")
        return False
    
    # Test collections
    print("[2/5] Checking collections...")
    db = mongo.get_db()
    collections = db.list_collection_names()
    required_collections = [
        "sessions",
        "planner_logs",
        "executor_logs",
        "metrics",
        "memory_store",
        "messages"
    ]
    
    for col in required_collections:
        if col in collections:
            print(f"  [✓] {col}")
        else:
            print(f"  [✗] {col} - MISSING")
    print()
    
    # Get MongoDB memory instance
    print("[3/5] Initializing MongoDB memory manager...")
    mongo_mem = get_mongo_memory()
    if not mongo_mem.is_connected():
        print("[✗] Failed to initialize memory manager")
        return False
    print("[✓] Memory manager initialized\n")
    
    # Test write operation
    print("[4/5] Testing write/read operations...")
    try:
        # Create test session
        test_user_id = 99999
        session_id = mongo_mem.create_session(test_user_id, {
            "test": True,
            "timestamp": datetime.now().isoformat()
        })
        print(f"  [✓] Session created: {session_id}")
        
        # Store test message
        msg_id = mongo_mem.store_message(
            test_user_id,
            "user",
            "Test message untuk verify MongoDB setup",
            metadata={"test": True}
        )
        print(f"  [✓] Message stored: {msg_id}")
        
        # Log test action
        log_id = mongo_mem.log_executor_action(
            test_user_id,
            "test_action",
            {"test": True},
            status="success"
        )
        print(f"  [✓] Action logged: {log_id}")
        
        # Record test metric
        metric_id = mongo_mem.record_metric(
            test_user_id,
            "test_metric",
            42,
            tags={"test": True}
        )
        print(f"  [✓] Metric recorded: {metric_id}")
        
        # Retrieve test data
        messages = mongo_mem.get_messages(test_user_id)
        print(f"  [✓] Retrieved {len(messages)} message(s)")
        
        # Get analytics
        analytics = mongo_mem.get_user_analytics(test_user_id)
        print(f"  [✓] Analytics retrieved: {len(str(analytics))} chars")
        
    except Exception as e:
        print(f"  [✗] Write/read test failed: {e}")
        return False
    print()
    
    # Display summary
    print("[5/5] MongoDB Long-term Memory Setup Summary")
    print("-" * 60)
    print(f"  Database: VokasiTeraDB")
    print(f"  Collections: {', '.join(required_collections)}")
    print(f"  Status: ✓ Ready for use")
    print("-" * 60 + "\n")
    
    # API endpoints
    print("📡 Available API Endpoints:")
    print("  - GET /mongodb-status              (Check connection)")
    print("  - GET /analytics/{user_id}         (User analytics)")
    print("  - GET /long-term-history/{user_id} (Full history)")
    print("  - GET /metrics/{user_id}/{type}    (Performance metrics)")
    print("  - GET /execution-logs/{user_id}    (Action logs)")
    print()
    
    # Usage examples
    print("💡 Usage Examples:")
    print("  from core.mongo_integration import MongoDBIntegration")
    print()
    print("  # Log conversation")
    print("  MongoDBIntegration.log_conversation(user_id, 'user', prompt)")
    print()
    print("  # Log action")
    print("  MongoDBIntegration.log_executor_action(")
    print("      user_id, 'create_group', 'tool_name', input_data, result")
    print("  )")
    print()
    print("  # Get user insights")
    print("  insights = MongoDBIntegration.get_user_context_insights(user_id)")
    print()
    
    # Next steps
    print("📋 Next Steps:")
    print("  1. Add imports to your agent files:")
    print("     from core.mongo_integration import MongoDBIntegration")
    print()
    print("  2. Add logging to key functions:")
    print("     - In main.py: Log all conversations")
    print("     - In executor_node.py: Log all actions")
    print("     - In planner_node.py: Log reasoning")
    print()
    print("  3. See MONGODB_LONGTERM_MEMORY.md untuk detailed docs")
    print()
    
    return True


def show_environment_variables():
    """Show required environment variables"""
    print("\n" + "="*60)
    print("📝 MongoDB Environment Variables")
    print("="*60 + "\n")
    
    print("Add these to .env file:\n")
    print("# MongoDB Configuration")
    print("MONGO_HOST=localhost")
    print("MONGO_PORT=27017")
    print("MONGO_DB=VokasiTeraDB")
    print("# Optional - if authentication required:")
    print("# MONGO_USERNAME=your_username")
    print("# MONGO_PASSWORD=your_password")
    print()


def main():
    """Main entry point"""
    
    # Show environment variables
    show_environment_variables()
    
    # Run setup test
    success = test_mongodb_setup()
    
    if success:
        print("✓ MongoDB Long-term Memory Setup Successful!")
        print("\nYou can now integrate MongoDB logging into your agent.")
        sys.exit(0)
    else:
        print("✗ MongoDB Long-term Memory Setup Failed!")
        print("\nPlease check:")
        print("  1. MongoDB is running (mongod)")
        print("  2. .env file has correct MONGO_HOST, MONGO_PORT, MONGO_DB")
        print("  3. Database 'VokasiTeraDB' exists in MongoDB")
        print("  4. Firewall allows connection to MongoDB port")
        sys.exit(1)


if __name__ == "__main__":
    main()
