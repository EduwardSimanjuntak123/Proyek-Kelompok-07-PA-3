"""
Integrasi MongoDB ke dalam Agent Conversation Flow
Module ini menyediakan helper functions untuk log ke MongoDB
dari berbagai bagian agent (planner, executor, answer nodes)
"""

from core.mongo_memory import get_mongo_memory
from datetime import datetime
from typing import Dict, Any, Optional, List
import logging

logger = logging.getLogger(__name__)


class MongoDBIntegration:
    """Helper class untuk integrasi MongoDB dengan agent workflow"""
    
    @staticmethod
    def log_conversation(user_id: int, role: str, content: str, metadata: Dict = None):
        """
        Log conversation message ke MongoDB
        
        Gunakan di: main.py, nodes untuk simpan all messages
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                mongo.store_message(user_id, role, content, metadata)
                logger.debug(f"[MONGO-INT] Logged {role} message for user {user_id}")
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to log conversation: {e}")
    
    @staticmethod
    def log_planner_reasoning(user_id: int, prompt: str, reasoning: str, 
                             selected_action: str) -> Optional[str]:
        """
        Log planner layer reasoning dan action selection
        
        Gunakan di: planner_node.py untuk track decision making
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                details = {
                    "prompt": prompt,
                    "reasoning": reasoning,
                    "selected_action": selected_action
                }
                return mongo.log_planner_action(user_id, "reasoning_and_selection", details)
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to log planner reasoning: {e}")
        return None
    
    @staticmethod
    def log_executor_action(user_id: int, action_type: str, tool_used: str,
                           input_data: Dict, result: Any, status: str = "success",
                           error: Optional[str] = None) -> Optional[str]:
        """
        Log executor layer action execution
        
        Gunakan di: executor_node.py untuk track apa yang dilakukan
        
        Args:
            user_id: User ID
            action_type: Jenis action (create_group, generate_jadwal, etc)
            tool_used: Tool mana yang digunakan (JadwalSeminarTools, etc)
            input_data: Input ke tool
            result: Hasil dari tool
            status: "success" atau "error"
            error: Error message jika ada
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                details = {
                    "tool_used": tool_used,
                    "input_data": input_data,
                    "result": result,
                    "error": error,
                    "execution_time": datetime.now().isoformat()
                }
                return mongo.log_executor_action(user_id, action_type, details, status)
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to log executor action: {e}")
        return None
    
    @staticmethod
    def record_response_metric(user_id: int, response_time_ms: float, 
                              token_count: int = 0, quality_score: float = 1.0):
        """
        Record performance metrics
        
        Gunakan di: api.py untuk track response quality
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                mongo.record_metric(user_id, "response_time_ms", response_time_ms,
                                  tags={"quality_score": quality_score, "tokens": token_count})
                logger.debug(f"[MONGO-INT] Recorded response metric: {response_time_ms}ms")
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to record metric: {e}")
    
    @staticmethod
    def store_user_insight(user_id: int, insight_type: str, content: Any,
                          tags: List[str] = None) -> Optional[str]:
        """
        Store long-term insight tentang user behavior/preference
        
        Gunakan untuk: learning system, pattern recognition
        
        Args:
            user_id: User ID
            insight_type: Jenis insight (user_preference, pattern, behavior, etc)
            content: Insight content
            tags: Tags untuk organizing
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                return mongo.store_memory(user_id, insight_type, content, tags)
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to store insight: {e}")
        return None
    
    @staticmethod
    def get_user_context_insights(user_id: int, limit: int = 10) -> List[Dict]:
        """
        Retrieve user insights untuk context awareness di agent
        
        Gunakan di: planner_node.py untuk context-aware planning
        """
        try:
            mongo = get_mongo_memory()
            if mongo.is_connected():
                memories = mongo.get_memories(user_id, memory_type="user_preference", limit=limit)
                logger.debug(f"[MONGO-INT] Retrieved {len(memories)} user insights")
                return memories
        except Exception as e:
            logger.warning(f"[MONGO-INT] Failed to retrieve insights: {e}")
        return []
    
    @staticmethod
    def log_grouping_action(user_id: int, prodi: str, kategori_pa: str,
                           num_students: int, num_groups: int,
                           result: Dict) -> Optional[str]:
        """
        Log specific action: grouping creation
        
        Detailed logging untuk track grouping decisions
        """
        return MongoDBIntegration.log_executor_action(
            user_id,
            "create_group",
            "GroupingTool",
            {
                "prodi": prodi,
                "kategori_pa": kategori_pa,
                "num_students": num_students
            },
            {
                "num_groups": num_groups,
                "groups": result
            }
        )
    
    @staticmethod
    def log_jadwal_action(user_id: int, action: str, tanggal: str,
                         ruangan_count: int, durasi_menit: int,
                         result: Dict) -> Optional[str]:
        """
        Log specific action: jadwal seminar creation
        
        Detailed logging untuk track jadwal generation
        """
        return MongoDBIntegration.log_executor_action(
            user_id,
            "generate_jadwal",
            "JadwalSeminarTools",
            {
                "action": action,
                "tanggal": tanggal,
                "ruangan_count": ruangan_count,
                "durasi_menit": durasi_menit
            },
            result
        )
    
    @staticmethod
    def log_pembimbing_action(user_id: int, kategori_pa: str,
                             num_groups: int, result: Dict) -> Optional[str]:
        """
        Log specific action: pembimbing assignment
        
        Detailed logging untuk track pembimbing assignment
        """
        return MongoDBIntegration.log_executor_action(
            user_id,
            "assign_pembimbing",
            "PembimbingAssignmentTool",
            {
                "kategori_pa": kategori_pa,
                "num_groups": num_groups
            },
            result
        )


# Convenience functions
def log_to_mongo(user_id: int, role: str, content: str, metadata: Dict = None):
    """Quick function to log to MongoDB"""
    MongoDBIntegration.log_conversation(user_id, role, content, metadata)


def get_user_insights(user_id: int) -> List[Dict]:
    """Quick function to get user insights"""
    return MongoDBIntegration.get_user_context_insights(user_id)
