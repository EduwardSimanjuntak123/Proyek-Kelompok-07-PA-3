"""
MongoDB Long-term Memory Manager
Untuk menyimpan analytics, logs, insights, dan full conversation history
"""

from datetime import datetime, timedelta
from typing import List, Dict, Optional, Any
from pymongo import ASCENDING, DESCENDING
from bson.objectid import ObjectId
import json
import logging

from core.mongodb import get_mongodb

logger = logging.getLogger(__name__)


class MongoDBMemory:
    """Manager untuk MongoDB long-term memory"""
    
    def __init__(self):
        self.mongo = get_mongodb()
        self.sessions_col = self.mongo.get_collection("sessions")
        self.planner_logs_col = self.mongo.get_collection("planner_logs")
        self.executor_logs_col = self.mongo.get_collection("executor_logs")
        self.metrics_col = self.mongo.get_collection("metrics")
        self.memory_store_col = self.mongo.get_collection("memory_store")
        self.messages_col = self.mongo.get_collection("messages")
    
    def is_connected(self) -> bool:
        """Check if MongoDB is connected"""
        return self.mongo.is_connected()
    
    # ==================== SESSIONS ====================
    
    def create_session(self, user_id: int, session_data: Dict) -> Optional[str]:
        """
        Buat session baru
        
        Args:
            user_id: User ID
            session_data: Data session (prodi, kategori_pa, angkatan, dll)
        
        Returns:
            Session ID (MongoDB ObjectId as string)
        """
        try:
            session_doc = {
                "user_id": user_id,
                "created_at": datetime.now(),
                "updated_at": datetime.now(),
                "data": session_data,
                "status": "active"
            }
            
            result = self.sessions_col.insert_one(session_doc)
            logger.info(f"[MONGO] Created session for user {user_id}: {result.inserted_id}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error creating session: {e}")
            return None
    
    def get_session(self, user_id: int) -> Optional[Dict]:
        """Get latest active session untuk user"""
        try:
            session = self.sessions_col.find_one(
                {"user_id": user_id, "status": "active"},
                sort=[("created_at", DESCENDING)]
            )
            return session
        except Exception as e:
            logger.error(f"[MONGO] Error getting session: {e}")
            return None
    
    def update_session(self, user_id: int, session_data: Dict) -> bool:
        """Update session data"""
        try:
            result = self.sessions_col.update_one(
                {"user_id": user_id, "status": "active"},
                {"$set": {"updated_at": datetime.now(), "data": session_data}},
                sort=[("created_at", DESCENDING)]
            )
            return result.modified_count > 0
        except Exception as e:
            logger.error(f"[MONGO] Error updating session: {e}")
            return False
    
    def close_session(self, user_id: int) -> bool:
        """Close session untuk user"""
        try:
            result = self.sessions_col.update_one(
                {"user_id": user_id, "status": "active"},
                {"$set": {"status": "closed", "closed_at": datetime.now()}}
            )
            return result.modified_count > 0
        except Exception as e:
            logger.error(f"[MONGO] Error closing session: {e}")
            return False
    
    # ==================== MESSAGES ====================
    
    def store_message(self, user_id: int, role: str, content: str, 
                     metadata: Optional[Dict] = None) -> Optional[str]:
        """
        Store message ke MongoDB untuk long-term history
        
        Args:
            user_id: User ID
            role: "user" atau "assistant"
            content: Message content
            metadata: Optional metadata (action, model, tokens, dll)
        
        Returns:
            Message ID
        """
        try:
            message_doc = {
                "user_id": user_id,
                "role": role,
                "content": content,
                "timestamp": datetime.now(),
                "metadata": metadata or {}
            }
            
            result = self.messages_col.insert_one(message_doc)
            logger.debug(f"[MONGO] Stored {role} message for user {user_id}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error storing message: {e}")
            return None
    
    def get_messages(self, user_id: int, limit: int = 100, 
                     start_date: Optional[datetime] = None) -> List[Dict]:
        """
        Get messages untuk user
        
        Args:
            user_id: User ID
            limit: Jumlah messages (default 100, max 1000)
            start_date: Filter messages dari date tertentu
        
        Returns:
            List of messages
        """
        try:
            limit = min(limit, 1000)  # Max 1000
            
            query = {"user_id": user_id}
            if start_date:
                query["timestamp"] = {"$gte": start_date}
            
            messages = list(self.messages_col.find(query)
                          .sort("timestamp", ASCENDING)
                          .limit(limit))
            
            return messages
        except Exception as e:
            logger.error(f"[MONGO] Error getting messages: {e}")
            return []
    
    def get_conversation_history(self, user_id: int, days: int = 30) -> List[Dict]:
        """
        Get full conversation history untuk user dalam n days terakhir
        
        Args:
            user_id: User ID
            days: Number of days to retrieve (default 30)
        
        Returns:
            Conversation history dengan timestamps
        """
        try:
            start_date = datetime.now() - timedelta(days=days)
            messages = self.get_messages(user_id, limit=1000, start_date=start_date)
            
            # Convert ObjectId to string untuk JSON serialization
            for msg in messages:
                msg["_id"] = str(msg.get("_id", ""))
            
            return messages
        except Exception as e:
            logger.error(f"[MONGO] Error getting conversation history: {e}")
            return []
    
    # ==================== PLANNER LOGS ====================
    
    def log_planner_action(self, user_id: int, action_type: str, 
                          details: Dict, status: str = "success") -> Optional[str]:
        """
        Log planner layer action
        
        Args:
            user_id: User ID
            action_type: Type of action (plan_generation, route_selection, etc)
            details: Action details (prompt, plan, route, etc)
            status: "success" atau "error"
        
        Returns:
            Log ID
        """
        try:
            log_doc = {
                "user_id": user_id,
                "timestamp": datetime.now(),
                "action_type": action_type,
                "status": status,
                "details": details
            }
            
            result = self.planner_logs_col.insert_one(log_doc)
            logger.debug(f"[MONGO] Logged planner action for user {user_id}: {action_type}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error logging planner action: {e}")
            return None
    
    def get_planner_logs(self, user_id: int, limit: int = 100) -> List[Dict]:
        """Get planner logs untuk user"""
        try:
            logs = list(self.planner_logs_col.find({"user_id": user_id})
                       .sort("timestamp", DESCENDING)
                       .limit(limit))
            return logs
        except Exception as e:
            logger.error(f"[MONGO] Error getting planner logs: {e}")
            return []
    
    # ==================== EXECUTOR LOGS ====================
    
    def log_executor_action(self, user_id: int, action_type: str, 
                           details: Dict, status: str = "success") -> Optional[str]:
        """
        Log executor layer action
        
        Args:
            user_id: User ID
            action_type: Type of action (create_group, generate_jadwal, etc)
            details: Execution details (tool_used, result, error, etc)
            status: "success" atau "error"
        
        Returns:
            Log ID
        """
        try:
            log_doc = {
                "user_id": user_id,
                "timestamp": datetime.now(),
                "action_type": action_type,
                "execution_status": status,
                "details": details
            }
            
            result = self.executor_logs_col.insert_one(log_doc)
            logger.debug(f"[MONGO] Logged executor action for user {user_id}: {action_type}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error logging executor action: {e}")
            return None
    
    def get_executor_logs(self, user_id: int, limit: int = 100) -> List[Dict]:
        """Get executor logs untuk user"""
        try:
            logs = list(self.executor_logs_col.find({"user_id": user_id})
                       .sort("timestamp", DESCENDING)
                       .limit(limit))
            return logs
        except Exception as e:
            logger.error(f"[MONGO] Error getting executor logs: {e}")
            return []
    
    # ==================== METRICS ====================
    
    def record_metric(self, user_id: int, metric_type: str, 
                     value: Any, tags: Optional[Dict] = None) -> Optional[str]:
        """
        Record metric untuk monitoring dan analytics
        
        Args:
            user_id: User ID
            metric_type: Type of metric (response_time, token_count, quality_score, etc)
            value: Metric value
            tags: Optional tags untuk filtering/grouping
        
        Returns:
            Metric ID
        """
        try:
            metric_doc = {
                "user_id": user_id,
                "timestamp": datetime.now(),
                "metric_type": metric_type,
                "value": value,
                "tags": tags or {}
            }
            
            result = self.metrics_col.insert_one(metric_doc)
            logger.debug(f"[MONGO] Recorded metric for user {user_id}: {metric_type}={value}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error recording metric: {e}")
            return None
    
    def get_metrics(self, user_id: int, metric_type: str, 
                   days: int = 7, limit: int = 100) -> List[Dict]:
        """
        Get metrics untuk user dan metric type
        
        Args:
            user_id: User ID
            metric_type: Type of metric
            days: Retrieve metrics from last n days
            limit: Max number of metrics
        
        Returns:
            List of metrics
        """
        try:
            start_date = datetime.now() - timedelta(days=days)
            
            metrics = list(self.metrics_col.find({
                "user_id": user_id,
                "metric_type": metric_type,
                "timestamp": {"$gte": start_date}
            }).sort("timestamp", DESCENDING).limit(limit))
            
            return metrics
        except Exception as e:
            logger.error(f"[MONGO] Error getting metrics: {e}")
            return []
    
    def get_metric_stats(self, user_id: int, metric_type: str, 
                        days: int = 7) -> Dict:
        """
        Get statistics untuk metric type (min, max, avg, count)
        
        Args:
            user_id: User ID
            metric_type: Type of metric
            days: Period in days
        
        Returns:
            Statistics dict
        """
        try:
            start_date = datetime.now() - timedelta(days=days)
            
            pipeline = [
                {
                    "$match": {
                        "user_id": user_id,
                        "metric_type": metric_type,
                        "timestamp": {"$gte": start_date}
                    }
                },
                {
                    "$group": {
                        "_id": None,
                        "count": {"$sum": 1},
                        "min": {"$min": "$value"},
                        "max": {"$max": "$value"},
                        "avg": {"$avg": "$value"},
                        "sum": {"$sum": "$value"}
                    }
                }
            ]
            
            result = list(self.metrics_col.aggregate(pipeline))
            if result:
                return result[0]
            return {"count": 0, "min": 0, "max": 0, "avg": 0, "sum": 0}
        except Exception as e:
            logger.error(f"[MONGO] Error getting metric stats: {e}")
            return {}
    
    # ==================== MEMORY STORE ====================
    
    def store_memory(self, user_id: int, memory_type: str, 
                    content: Any, tags: Optional[List[str]] = None) -> Optional[str]:
        """
        Store long-term memory item (insights, patterns, preferences, etc)
        
        Args:
            user_id: User ID
            memory_type: Type of memory (user_preference, pattern, insight, etc)
            content: Memory content
            tags: List of tags untuk organizing memories
        
        Returns:
            Memory ID
        """
        try:
            memory_doc = {
                "user_id": user_id,
                "memory_type": memory_type,
                "content": content,
                "tags": tags or [],
                "created_at": datetime.now(),
                "updated_at": datetime.now(),
                "access_count": 0
            }
            
            result = self.memory_store_col.insert_one(memory_doc)
            logger.debug(f"[MONGO] Stored {memory_type} memory for user {user_id}")
            return str(result.inserted_id)
        except Exception as e:
            logger.error(f"[MONGO] Error storing memory: {e}")
            return None
    
    def get_memories(self, user_id: int, memory_type: Optional[str] = None,
                    tags: Optional[List[str]] = None, limit: int = 100) -> List[Dict]:
        """
        Get memories untuk user
        
        Args:
            user_id: User ID
            memory_type: Filter by memory type
            tags: Filter by tags (any match)
            limit: Max results
        
        Returns:
            List of memories
        """
        try:
            query = {"user_id": user_id}
            
            if memory_type:
                query["memory_type"] = memory_type
            
            if tags:
                query["tags"] = {"$in": tags}
            
            memories = list(self.memory_store_col.find(query)
                          .sort("updated_at", DESCENDING)
                          .limit(limit))
            
            return memories
        except Exception as e:
            logger.error(f"[MONGO] Error getting memories: {e}")
            return []
    
    def update_memory(self, memory_id: str, content: Any, 
                     tags: Optional[List[str]] = None) -> bool:
        """Update memory content"""
        try:
            update_data = {
                "content": content,
                "updated_at": datetime.now(),
                "$inc": {"access_count": 1}
            }
            
            if tags is not None:
                update_data["tags"] = tags
            
            result = self.memory_store_col.update_one(
                {"_id": ObjectId(memory_id)},
                {"$set": update_data}
            )
            
            return result.modified_count > 0
        except Exception as e:
            logger.error(f"[MONGO] Error updating memory: {e}")
            return False
    
    # ==================== USER ANALYTICS ====================
    
    def get_user_analytics(self, user_id: int) -> Dict:
        """
        Get comprehensive analytics untuk user
        
        Returns:
            Analytics summary (total messages, actions, last activity, etc)
        """
        try:
            messages = self.messages_col.count_documents({"user_id": user_id})
            planner_actions = self.planner_logs_col.count_documents({"user_id": user_id})
            executor_actions = self.executor_logs_col.count_documents({"user_id": user_id})
            
            # Get last activity
            last_message = self.messages_col.find_one(
                {"user_id": user_id},
                sort=[("timestamp", DESCENDING)]
            )
            
            last_activity = last_message["timestamp"] if last_message else None
            
            # Get session info
            session = self.get_session(user_id)
            
            return {
                "user_id": user_id,
                "total_messages": messages,
                "total_planner_actions": planner_actions,
                "total_executor_actions": executor_actions,
                "last_activity": last_activity,
                "session_info": session
            }
        except Exception as e:
            logger.error(f"[MONGO] Error getting user analytics: {e}")
            return {}
    
    # ==================== CLEANUP ====================
    
    def cleanup_old_data(self, days: int = 90) -> Dict:
        """
        Delete old data untuk manage storage
        
        Args:
            days: Delete data older than n days
        
        Returns:
            Cleanup stats
        """
        try:
            cutoff_date = datetime.now() - timedelta(days=days)
            
            stats = {
                "messages_deleted": 0,
                "planner_logs_deleted": 0,
                "executor_logs_deleted": 0,
                "metrics_deleted": 0
            }
            
            # Delete old messages
            result = self.messages_col.delete_many({"timestamp": {"$lt": cutoff_date}})
            stats["messages_deleted"] = result.deleted_count
            
            # Delete old planner logs
            result = self.planner_logs_col.delete_many({"timestamp": {"$lt": cutoff_date}})
            stats["planner_logs_deleted"] = result.deleted_count
            
            # Delete old executor logs
            result = self.executor_logs_col.delete_many({"timestamp": {"$lt": cutoff_date}})
            stats["executor_logs_deleted"] = result.deleted_count
            
            # Delete old metrics
            result = self.metrics_col.delete_many({"timestamp": {"$lt": cutoff_date}})
            stats["metrics_deleted"] = result.deleted_count
            
            logger.info(f"[MONGO] Cleanup completed: {stats}")
            return stats
        except Exception as e:
            logger.error(f"[MONGO] Error during cleanup: {e}")
            return {}


# Singleton instance
_mongo_memory_instance: Optional[MongoDBMemory] = None


def get_mongo_memory() -> MongoDBMemory:
    """Get MongoDB memory singleton"""
    global _mongo_memory_instance
    if _mongo_memory_instance is None:
        _mongo_memory_instance = MongoDBMemory()
    return _mongo_memory_instance
