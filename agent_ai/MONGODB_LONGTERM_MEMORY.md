"""
MongoDB Long-term Memory System Documentation

OVERVIEW:
=========
MongoDB digunakan untuk menyimpan long-term memory dan analytics data.
Redis tetap digunakan untuk short-term memory (session cache).

KOLEKSI MONGODB:
================

1. sessions
   - Menyimpan user session data
   - Fields: user_id, created_at, updated_at, data, status
   - Gunakan untuk: Track user context, session lifecycle
   
2. messages
   - Menyimpan semua messages (full history)
   - Fields: user_id, role (user/assistant), content, timestamp, metadata
   - Gunakan untuk: Full conversation history, message search
   
3. planner_logs
   - Menyimpan logs dari planner layer
   - Fields: user_id, timestamp, action_type, status, details
   - Gunakan untuk: Debug planner decisions, track reasoning
   
4. executor_logs
   - Menyimpan logs dari executor layer
   - Fields: user_id, timestamp, action_type, execution_status, details
   - Gunakan untuk: Track action execution, audit trail
   
5. metrics
   - Menyimpan performance metrics
   - Fields: user_id, timestamp, metric_type, value, tags
   - Gunakan untuk: Monitoring, performance analysis
   
6. memory_store
   - Menyimpan long-term insights tentang user
   - Fields: user_id, memory_type, content, tags, created_at, updated_at
   - Gunakan untuk: User preference learning, pattern recognition

INTEGRASI DENGAN AGENT:
=======================

Import dari: core.mongo_integration import MongoDBIntegration

Contoh penggunaan:

1. Log conversation message:
   MongoDBIntegration.log_conversation(user_id, "user", prompt_text)
   MongoDBIntegration.log_conversation(user_id, "assistant", response_text)

2. Log planner reasoning:
   MongoDBIntegration.log_planner_reasoning(
       user_id,
       prompt,
       reasoning_text,
       selected_action
   )

3. Log executor action:
   MongoDBIntegration.log_executor_action(
       user_id,
       "create_group",
       "GroupingTool",
       input_data,
       result,
       status="success"
   )

4. Record metrics:
   MongoDBIntegration.record_response_metric(
       user_id,
       response_time_ms=150,
       token_count=200,
       quality_score=0.95
   )

5. Store user insights:
   MongoDBIntegration.store_user_insight(
       user_id,
       "user_preference",
       {"prefers_small_groups": True},
       tags=["grouping", "preference"]
   )

6. Get user insights:
   insights = MongoDBIntegration.get_user_context_insights(user_id)

API ENDPOINTS:
==============

1. GET /mongodb-status
   - Check MongoDB connection status
   - Returns: connected status, collections info

2. GET /analytics/{user_id}
   - Get comprehensive user analytics
   - Returns: total messages, actions, metrics, last activity

3. GET /long-term-history/{user_id}?days=30&limit=100
   - Get full conversation history from MongoDB
   - Args: days (default 30), limit (default 100, max 1000)
   - Returns: All messages with timestamps

4. GET /metrics/{user_id}/{metric_type}?days=7
   - Get specific metrics untuk user
   - Args: metric_type, days (default 7)
   - Returns: Metrics dan statistics (min, max, avg, count)

5. GET /execution-logs/{user_id}?limit=50
   - Get executor logs untuk user
   - Args: limit (default 50, max 200)
   - Returns: List of executor actions

MEMORY LIFECYCLE:
=================

SHORT-TERM (Redis):
- Session cache (fast, ~1-5ms)
- Last 20 messages
- User preferences
- Current session state
- TTL: 24 hours

LONG-TERM (MongoDB):
- Full conversation history (unlimited)
- All planner/executor logs
- Performance metrics
- User insights
- Session records
- No TTL (indefinite storage)

HYBRID APPROACH:
1. API receives request
2. Load short-term context dari Redis (fast)
3. Agent processes
4. Save to Redis immediately (instant response)
5. Save to MongoDB asynchronously (background)
6. User gets response immediately with Redis data
7. Long-term analytics available from MongoDB

CONFIGURATION:
===============

.env file settings:
MONGO_HOST=localhost
MONGO_PORT=27017
MONGO_DB=VokasiTeraDB
MONGO_USERNAME=  # optional
MONGO_PASSWORD=  # optional

Default connection: mongodb://localhost:27017/VokasiTeraDB

PERFORMANCE NOTES:
==================

1. MongoDB is indexed untuk common queries:
   - user_id (all collections)
   - timestamp (messages, planner_logs, executor_logs, metrics)
   - metric_type (metrics)
   - action_type (planner_logs, executor_logs)
   - Compound indexes untuk fast filtering

2. Recommended:
   - Run MongoDB on separate server
   - Use connection pooling
   - Regular index maintenance
   - Archive old data (>90 days) untuk manage storage

3. Cleanup:
   - Call: mongo_mem.cleanup_old_data(days=90)
   - Deletes messages, logs, metrics older than n days
   - Keeps memory_store (insights)

BEST PRACTICES:
===============

1. Always log both successes dan errors untuk audit trail
2. Use meaningful tags untuk organize memories
3. Record metrics untuk performance monitoring
4. Retrieve insights untuk context-aware planning
5. Call cleanup periodically untuk manage storage
6. Use MongoDB for analytics, Redis for immediate data

TROUBLESHOOTING:
=================

If MongoDB not connected:
- Check MONGO_HOST, MONGO_PORT in .env
- Verify MongoDB service is running
- Check firewall ports
- See logs: [MONGODB] connection errors

If collection missing:
- Collections auto-created on startup
- Check permissions on database
- Manually create: use VokasiTeraDB; db.createCollection("collection_name")

If indexing fails:
- Non-critical, logs as warning
- Query still works, just slower
- Manually create indexes if needed

EXAMPLE WORKFLOW:
=================

User sends grouping request:
1. API receives request
2. Load context dari Redis (1-5ms)
3. Log to MongoDB: store_message(user_id, "user", prompt)
4. Call agent
5. Agent logs to MongoDB: log_executor_action(...)
6. Save response to Redis
7. Save to MongoDB: store_message(user_id, "assistant", response)
8. Return response to user immediately
9. Background: MongoDB has full audit trail

Later, check analytics:
1. GET /analytics/user_123
2. MongoDB aggregates: messages, logs, metrics
3. Return comprehensive overview

Even later, retrieve old conversation:
1. GET /long-term-history/user_123?days=90
2. MongoDB finds all messages dari 90 days ago
3. Return full history for analysis
"""

# Contoh kode untuk integrasi di berbagai node:

"""
PLANNER NODE INTEGRATION:
-------------------------

from core.mongo_integration import MongoDBIntegration

def execute_planner(prompt, user_id, state):
    # Do planning logic...
    reasoning = "Determined user wants to create groups..."
    selected_action = "create_group"
    
    # Log ke MongoDB
    MongoDBIntegration.log_planner_reasoning(
        user_id,
        prompt,
        reasoning,
        selected_action
    )
    
    # Get user insights untuk context-aware planning
    insights = MongoDBIntegration.get_user_context_insights(user_id)
    
    return selected_action


EXECUTOR NODE INTEGRATION:
---------------------------

from core.mongo_integration import MongoDBIntegration

def execute_action(action, user_id, state):
    try:
        if action == "create_group":
            # Execute grouping...
            result = create_groups(...)
            
            # Log successful execution
            MongoDBIntegration.log_grouping_action(
                user_id,
                prodi=state["prodi"],
                kategori_pa=state["kategori_pa"],
                num_students=len(state["students"]),
                num_groups=len(result),
                result=result
            )
            
            # Record metric
            MongoDBIntegration.record_response_metric(
                user_id,
                response_time_ms=150,
                quality_score=0.95
            )
            
        elif action == "generate_jadwal":
            result = generate_jadwal(...)
            
            MongoDBIntegration.log_jadwal_action(
                user_id,
                action="save",
                tanggal=state["tanggal"],
                ruangan_count=len(state["ruangan"]),
                durasi_menit=state["durasi"],
                result=result
            )
        
        return result
    
    except Exception as e:
        # Log error
        MongoDBIntegration.log_executor_action(
            user_id,
            action,
            "tool_name",
            {},
            None,
            status="error",
            error=str(e)
        )
        raise


MAIN.PY INTEGRATION:
-------------------

from core.mongo_integration import MongoDBIntegration

def run_agent_chat(prompt, user_id, dosen_context, conversation_history):
    # Log user prompt
    MongoDBIntegration.log_conversation(user_id, "user", prompt)
    
    # Process with agent...
    result = agent_execute(prompt, dosen_context)
    
    # Log assistant response
    MongoDBIntegration.log_conversation(
        user_id,
        "assistant",
        result["result"],
        metadata={"action": result.get("action")}
    )
    
    return result
"""
