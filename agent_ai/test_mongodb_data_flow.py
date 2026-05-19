"""
Test script to verify MongoDB data flow and populate sample data for dashboard testing
"""

import sys
import json
import time
from datetime import datetime, timedelta
from core.mongo_memory import get_mongo_memory
from core.mongo_integration import MongoDBIntegration

def test_data_flow():
    """Test complete data flow - record and retrieve"""
    
    print("\n" + "="*60)
    print("Testing MongoDB Data Flow")
    print("="*60)
    
    mongo_mem = get_mongo_memory()
    test_user_id = 3607
    
    print(f"\n[TEST] Using user_id: {test_user_id}")
    
    # 1. Test session creation
    print("\n[1] Testing Session Creation...")
    session_data = {
        "user_id": test_user_id,
        "dosen_context": {"prodi_id": 1, "kategori_pa": 1},
        "start_time": datetime.now()
    }
    session_id = mongo_mem.create_session(test_user_id, session_data)
    print(f"    ✓ Session created: {session_id}")
    
    # 2. Test message storage
    print("\n[2] Testing Message Storage...")
    mongo_mem.store_message(
        test_user_id,
        "user",
        "Buat grouping untuk PA 3",
        metadata={"source": "api"}
    )
    mongo_mem.store_message(
        test_user_id,
        "assistant",
        "Grouping berhasil dibuat dengan 5 kelompok",
        metadata={"action": "create_grouping"}
    )
    print("    ✓ Messages stored")
    
    # 3. Test executor action logging
    print("\n[3] Testing Executor Action Logging...")
    mongo_mem.log_executor_action(
        test_user_id,
        "create_grouping",
        {"num_groups": 5, "total_students": 150},
        status="success"
    )
    print("    ✓ Executor action logged")
    
    # 4. Test metrics recording
    print("\n[4] Testing Metrics Recording...")
    
    # Record response times
    for i in range(5):
        response_time = 150 + (i * 50)  # 150, 200, 250, 300, 350
        mongo_mem.record_metric(
            test_user_id,
            "response_time_ms",
            response_time,
            tags={"action": "create_grouping", "attempt": i+1}
        )
    print("    ✓ Response time metrics recorded (5 samples)")
    
    # Record quality scores
    for i in range(5):
        quality_score = 0.8 + (i * 0.04)  # 0.80, 0.84, 0.88, 0.92, 0.96
        mongo_mem.record_metric(
            test_user_id,
            "response_quality",
            quality_score,
            tags={"result": "success"}
        )
    print("    ✓ Quality metrics recorded (5 samples)")
    
    # Record action counts
    for i in range(3):
        mongo_mem.record_metric(
            test_user_id,
            "action_count",
            1,
            tags={"action_type": "create_grouping"}
        )
    print("    ✓ Action count metrics recorded (3 samples)")
    
    # 5. Retrieve and display analytics
    print("\n[5] Retrieving Analytics Data...")
    
    messages = mongo_mem.get_messages(test_user_id, limit=100)
    print(f"    Total Messages: {len(messages)}")
    if messages:
        for msg in messages[-2:]:
            print(f"      - {msg.get('role', 'unknown')}: {msg.get('content', '')[:50]}...")
    
    exec_logs = mongo_mem.get_executor_logs(test_user_id)
    print(f"    Total Executor Actions: {len(exec_logs)}")
    if exec_logs:
        for log in exec_logs:
            print(f"      - {log.get('action_type')}: {log.get('status')}")
    
    response_metrics = mongo_mem.get_metrics(test_user_id, "response_time_ms", days=30)
    print(f"    Response Time Metrics: {len(response_metrics)} records")
    if response_metrics:
        times = [m.get('value', 0) for m in response_metrics]
        print(f"      - Min: {min(times)}ms, Max: {max(times)}ms, Avg: {sum(times)/len(times):.2f}ms")
    
    quality_metrics = mongo_mem.get_metrics(test_user_id, "response_quality", days=30)
    print(f"    Quality Metrics: {len(quality_metrics)} records")
    if quality_metrics:
        scores = [m.get('value', 0) for m in quality_metrics]
        print(f"      - Min: {min(scores):.2f}, Max: {max(scores):.2f}, Avg: {sum(scores)/len(scores):.2f}")
    
    action_metrics = mongo_mem.get_metrics(test_user_id, "action_count", days=30)
    print(f"    Action Count Metrics: {len(action_metrics)} records")
    
    # 6. Test user analytics aggregation
    print("\n[6] Testing User Analytics Aggregation...")
    analytics = mongo_mem.get_user_analytics(test_user_id)
    print(f"    Analytics Data:")
    for key, value in analytics.items():
        if key != 'session_info':
            print(f"      - {key}: {value}")
    
    # 7. Verify API endpoints would return correct data
    print("\n[7] Summary - Data Ready for API Endpoints...")
    print(f"    ✓ Session: {session_id}")
    print(f"    ✓ Messages: {len(messages)} stored")
    print(f"    ✓ Executor Actions: {len(exec_logs)} logged")
    print(f"    ✓ Response Time Samples: {len(response_metrics)}")
    print(f"    ✓ Quality Samples: {len(quality_metrics)}")
    print(f"    ✓ Action Counts: {len(action_metrics)}")
    
    print("\n" + "="*60)
    print("✅ Data flow test complete!")
    print("="*60)
    print("\nNext steps:")
    print("1. Run the API: python start_api.py")
    print("2. Visit: http://localhost:8000/ai-agent/analytics/debug")
    print("3. Check that user 3607 has data in all collections")
    print("="*60 + "\n")

if __name__ == "__main__":
    try:
        test_data_flow()
    except Exception as e:
        print(f"\n❌ Error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
