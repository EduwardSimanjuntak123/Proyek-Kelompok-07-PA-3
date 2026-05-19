"""
MongoDB Configuration dan Connection Setup
Untuk long-term memory storage (analytics, logging, insights)
"""

import os
from pymongo import MongoClient
from pymongo.errors import ConnectionFailure, ServerSelectionTimeoutError
from typing import Optional
from dotenv import load_dotenv
import logging

load_dotenv()

logger = logging.getLogger(__name__)

class MongoDBConnection:
    """MongoDB connection manager untuk long-term memory"""
    
    _instance: Optional['MongoDBConnection'] = None
    _client: Optional[MongoClient] = None
    _db = None
    
    def __new__(cls):
        if cls._instance is None:
            cls._instance = super(MongoDBConnection, cls).__new__(cls)
            cls._instance._initialize()
        return cls._instance
    
    def _initialize(self):
        """Initialize MongoDB connection"""
        try:
            # Get connection params from env or use defaults
            mongo_host = os.getenv("MONGO_HOST", "localhost")
            mongo_port = int(os.getenv("MONGO_PORT", 27017))
            mongo_db = os.getenv("MONGO_DB", "VokasiTeraDB")
            mongo_username = os.getenv("MONGO_USERNAME")
            mongo_password = os.getenv("MONGO_PASSWORD")
            
            # Build connection string
            if mongo_username and mongo_password:
                connection_string = f"mongodb://{mongo_username}:{mongo_password}@{mongo_host}:{mongo_port}"
            else:
                connection_string = f"mongodb://{mongo_host}:{mongo_port}"
            
            logger.info(f"[MONGODB] Connecting to {mongo_host}:{mongo_port}/{mongo_db}")
            
            # Create client with connection timeout
            self._client = MongoClient(
                connection_string,
                serverSelectionTimeoutMS=5000,
                connectTimeoutMS=10000,
                retryWrites=True
            )
            
            # Test connection
            self._client.admin.command('ping')
            logger.info(f"[MONGODB] ✓ Connected successfully to {mongo_db}")
            
            # Get database
            self._db = self._client[mongo_db]
            
            # Create collections if they don't exist
            self._create_collections()
            
        except (ConnectionFailure, ServerSelectionTimeoutError) as e:
            logger.error(f"[MONGODB] ✗ Connection failed: {e}")
            self._db = None
        except Exception as e:
            logger.error(f"[MONGODB] ✗ Initialization error: {e}")
            self._db = None
    
    def _create_collections(self):
        """Create required collections if they don't exist"""
        required_collections = [
            "sessions",
            "planner_logs",
            "executor_logs",
            "metrics",
            "memory_store",
            "messages"
        ]
        
        existing_collections = self._db.list_collection_names()
        
        for collection_name in required_collections:
            if collection_name not in existing_collections:
                self._db.create_collection(collection_name)
                logger.info(f"[MONGODB] Created collection: {collection_name}")
            
            # Create indexes for common queries
            self._create_indexes(collection_name)
    
    def _create_indexes(self, collection_name: str):
        """Create indexes untuk optimize queries"""
        collection = self._db[collection_name]
        
        try:
            if collection_name == "sessions":
                collection.create_index("user_id")
                collection.create_index("created_at")
                collection.create_index([("user_id", 1), ("created_at", -1)])
            
            elif collection_name == "planner_logs":
                collection.create_index("user_id")
                collection.create_index("timestamp")
                collection.create_index("action_type")
                collection.create_index([("user_id", 1), ("timestamp", -1)])
            
            elif collection_name == "executor_logs":
                collection.create_index("user_id")
                collection.create_index("timestamp")
                collection.create_index("execution_status")
                collection.create_index([("user_id", 1), ("timestamp", -1)])
            
            elif collection_name == "metrics":
                collection.create_index("user_id")
                collection.create_index("timestamp")
                collection.create_index("metric_type")
                collection.create_index([("user_id", 1), ("metric_type", 1)])
            
            elif collection_name == "memory_store":
                collection.create_index("user_id")
                collection.create_index("memory_type")
                collection.create_index("created_at")
                collection.create_index([("user_id", 1), ("memory_type", 1)])
            
            elif collection_name == "messages":
                collection.create_index("user_id")
                collection.create_index("timestamp")
                collection.create_index("role")
                collection.create_index([("user_id", 1), ("timestamp", -1)])
        
        except Exception as e:
            logger.warning(f"[MONGODB] Index creation warning for {collection_name}: {e}")
    
    def get_db(self):
        """Get MongoDB database instance"""
        return self._db
    
    def get_collection(self, collection_name: str):
        """Get specific collection"""
        if self._db is None:
            logger.error("[MONGODB] Database connection not available")
            return None
        return self._db[collection_name]
    
    def is_connected(self) -> bool:
        """Check if MongoDB is connected"""
        return self._db is not None
    
    def close(self):
        """Close MongoDB connection"""
        if self._client:
            self._client.close()
            logger.info("[MONGODB] Connection closed")
            self._db = None


# Singleton instance
def get_mongodb():
    """Get MongoDB connection singleton"""
    return MongoDBConnection()


def test_mongodb_connection():
    """Test MongoDB connection"""
    try:
        mongo = get_mongodb()
        if mongo.is_connected():
            print("[✓] MongoDB connected successfully")
            
            # Test write/read
            test_collection = mongo.get_collection("sessions")
            test_collection.insert_one({
                "test": True,
                "timestamp": __import__('datetime').datetime.now()
            })
            print("[✓] MongoDB read/write test passed")
            return True
        else:
            print("[✗] MongoDB not connected")
            return False
    except Exception as e:
        print(f"[✗] MongoDB test failed: {e}")
        return False


if __name__ == "__main__":
    test_mongodb_connection()
