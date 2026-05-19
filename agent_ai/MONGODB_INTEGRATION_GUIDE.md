"""
QUICK INTEGRATION GUIDE - MongoDB Long-term Memory

Langkah-langkah untuk fully integrate MongoDB ke semua parts of agent
"""

# STEP 1: Verify Setup
# ==================
# Run this first:
#   cd agent_ai
#   python setup_mongodb.py
#
# Expected: "✓ MongoDB Long-term Memory Setup Successful!"


# STEP 2: Already Integrated (Done!)
# ===================================
# ✅ main.py
#    - Logs user messages to MongoDB
#    - Logs assistant responses to MongoDB
#    - Logs errors to MongoDB
#
# ✅ api.py
#    - Added 5 new endpoints for long-term memory
#    - MongoDB logging in /agent endpoint
#    - Background: Messages saved to both Redis + MongoDB
#
# ✅ .env
#    - Added MongoDB configuration


# STEP 3: Ready for Integration in Other Nodes
# =============================================
#
# File: nodes/planner_node.py
# ---------------------------
# Add at top:
# from core.mongo_integration import MongoDBIntegration
#
# In planner function, add logging:
# 
#   def execute_planner(state):
#       # ... planner logic ...
#       
#       # Log planner reasoning
#       MongoDBIntegration.log_planner_reasoning(
#           user_id=state["user_id"],
#           prompt=state["messages"][-1]["content"],
#           reasoning="[Your reasoning text here]",
#           selected_action="[action name]"
#       )
#       
#       # Get user insights untuk context awareness
#       user_insights = MongoDBIntegration.get_user_context_insights(
#           state["user_id"],
#           limit=10
#       )
#       
#       # Use insights untuk better planning...
#       
#       return state
#
# File: nodes/executor_node.py
# ---------------------------
# Add at top:
# from core.mongo_integration import MongoDBIntegration
# import time
#
# In executor function, wrap actions:
#
#   def execute_actions(state):
#       action = state["plan"]["action"]
#       
#       try:
#           start_time = time.time()
#           
#           if action == "create_group":
#               result = create_groups(...)
#               
#               MongoDBIntegration.log_executor_action(
#                   state["user_id"],
#                   "create_group",
#                   "GroupingTool",
#                   input_data={...},
#                   result=result,
#                   status="success"
#               )
#           
#           elif action == "generate_jadwal":
#               result = generate_jadwal(...)
#               
#               MongoDBIntegration.log_executor_action(
#                   state["user_id"],
#                   "generate_jadwal",
#                   "JadwalSeminarTools",
#                   input_data={...},
#                   result=result,
#                   status="success"
#               )
#           
#           elif action == "assign_pembimbing":
#               result = assign_pembimbing(...)
#               
#               MongoDBIntegration.log_executor_action(
#                   state["user_id"],
#                   "assign_pembimbing",
#                   "PembimbingTool",
#                   input_data={...},
#                   result=result,
#                   status="success"
#               )
#           
#           # Record performance metric
#           elapsed_ms = (time.time() - start_time) * 1000
#           MongoDBIntegration.record_response_metric(
#               state["user_id"],
#               response_time_ms=elapsed_ms,
#               quality_score=0.95
#           )
#           
#           return state
#       
#       except Exception as e:
#           MongoDBIntegration.log_executor_action(
#               state["user_id"],
#               action,
#               "UnknownTool",
#               {},
#               None,
#               status="error",
#               error=str(e)
#           )
#           raise


# STEP 4: Testing Integration
# ===========================
# After adding logging to executor_node.py:
#
# 1. Start API:
#    python start_api.py
#
# 2. Make request through Laravel UI or API:
#    POST http://localhost:8002/agent
#    {
#      "prompt": "Bagi mahasiswa menjadi 3 kelompok",
#      "dosen_context": [...],
#      "user_id": 123
#    }
#
# 3. Check MongoDB logs:
#    curl http://localhost:8002/long-term-history/123
#    curl http://localhost:8002/execution-logs/123
#    curl http://localhost:8002/analytics/123
#
# 4. Expected output:
#    - Messages saved
#    - Executor actions logged
#    - Metrics recorded


# STEP 5: User Insights for Context-Aware Planning
# ================================================
# Optional but powerful - store learned patterns:
#
# In executor_node.py:
#
#   def execute_actions(state):
#       # ... do action ...
#       
#       # Learn and store user pattern
#       if action == "create_group" and result["success"]:
#           MongoDBIntegration.store_user_insight(
#               state["user_id"],
#               "user_preference",
#               {
#                   "prefers_group_size": result["group_size"],
#                   "preferred_criteria": state.get("grouping_criteria"),
#                   "timestamp": datetime.now().isoformat()
#               },
#               tags=["grouping", "preference", state["prodi"]]
#           )


# STEP 6: Monitoring Dashboard
# ============================
# Monitor real-time usage with API:
#
# Analytics for specific user:
#   GET /analytics/123
#
# Full conversation history (last 30 days):
#   GET /long-term-history/123?days=30&limit=100
#
# Response time metrics (last 7 days):
#   GET /metrics/123/response_time_ms?days=7
#
# Execution logs (all actions):
#   GET /execution-logs/123?limit=50
#
# Check MongoDB connection:
#   GET /mongodb-status


# STEP 7: Periodic Maintenance
# ============================
# Add to cron job atau background task:
#
# from core.mongo_memory import get_mongo_memory
#
# # Run monthly (cleanup data older than 90 days)
# mongo_mem = get_mongo_memory()
# stats = mongo_mem.cleanup_old_data(days=90)
# print(f"Cleaned up: {stats}")


# TROUBLESHOOTING
# ===============
#
# Problem: "MongoDB not connected" error
# Solution:
#   1. Start MongoDB: mongod
#   2. Check .env: MONGO_HOST=localhost, MONGO_PORT=27017
#   3. Run: python setup_mongodb.py
#
# Problem: "Collection not found"
# Solution:
#   Automatically created on first use
#   If not: python setup_mongodb.py creates them
#
# Problem: Slow queries
# Solution:
#   MongoDB automatically creates indexes
#   Check: db.sessions.getIndexes()
#
# Problem: Logs not appearing in MongoDB
# Solution:
#   1. Verify API logging: check /mongodb-status
#   2. Check api.py has MongoDB integration
#   3. Check .env MongoDB settings
#   4. Check MongoDB service is running


# FEATURE MATRIX
# ==============
#
# Feature                    Status  Location
# ─────────────────────────────────────────────────────
# Basic Memory Setup         ✅      core/mongodb.py
# Memory Manager             ✅      core/mongo_memory.py
# Main.py Integration        ✅      main.py
# API Endpoints              ✅      api.py
# Integration Helper         ✅      core/mongo_integration.py
# Planner Logging            ⏳      nodes/planner_node.py (TODO)
# Executor Logging           ⏳      nodes/executor_node.py (TODO)
# Answer Node Logging        ⏳      nodes/answer_node.py (TODO)
# User Insights Storage      ⏳      As needed
# Context-Aware Planning     ⏳      As needed
# Performance Monitoring     ✅      Via /metrics endpoint
# Audit Trail                ✅      Via /execution-logs endpoint


# SUMMARY
# =======
# 
# MongoDB Long-term Memory is ready to use!
#
# Current Status:
# ✅ MongoDB connected and configured
# ✅ Collections created with indexes
# ✅ Main conversation logging working
# ✅ 5 API endpoints available
# ✅ Integration helper ready
#
# Next: Add logging to other nodes (planner_node, executor_node)
# See step-by-step guide above
#
# Questions? Check MONGODB_LONGTERM_MEMORY.md or IMPLEMENTATION_MONGODB_SUMMARY.md
"""
