#!/usr/bin/env python3
"""
Redis Memory Management CLI Tool
Untuk query, clear, dan monitor Redis chat context
"""

import click
import redis
import json
from typing import Optional
from datetime import datetime
from core.redis_memory import RedisMemoryManager
import os
from dotenv import load_dotenv

load_dotenv()

# Initialize Redis
redis_host = os.getenv("REDIS_HOST", "localhost")
redis_port = int(os.getenv("REDIS_PORT", 6379))

@click.group()
def cli():
    """Redis Memory Management CLI"""
    pass


@cli.command()
def status():
    """Check Redis connection status"""
    try:
        mem = RedisMemoryManager(host=redis_host, port=redis_port)
        stats = mem.get_stats()
        
        click.echo("✓ Redis Status")
        click.echo(f"  Total Keys: {stats.get('total_keys', 0)}")
        click.echo(f"  Used Memory: {stats.get('used_memory', 'N/A')}")
        click.echo(f"  Connected Clients: {stats.get('connected_clients', 0)}")
        click.echo(f"  Uptime: {stats.get('uptime', 'N/A')}")
        click.echo(f"  Operations/sec: {stats.get('operations_per_sec', 0)}")
    except Exception as e:
        click.echo(f"✗ Redis Connection Error: {e}", err=True)


@cli.command()
@click.option('--user-id', type=int, required=True, help='User ID')
def view(user_id):
    """View chat context untuk specific user"""
    try:
        mem = RedisMemoryManager(host=redis_host, port=redis_port)
        context = mem.load_context(user_id)
        
        click.echo(f"\n📋 Chat Context for User {user_id}")
        click.echo("=" * 60)
        
        # Messages
        messages = context.get("messages", [])
        click.echo(f"\nMessages ({len(messages)} total):")
        for i, msg in enumerate(messages[-10:], 1):  # Show last 10
            role = "👤 User" if msg.get("role") == "user" else "🤖 Assistant"
            content = msg.get("content", "")[:60]
            ts = msg.get("timestamp", "N/A")[:19]
            click.echo(f"  {i}. [{ts}] {role}: {content}...")
        
        # Preferences
        prefs = context.get("preferences", {})
        if prefs:
            click.echo(f"\nPreferences:")
            for key, val in prefs.items():
                click.echo(f"  - {key}: {val}")
        else:
            click.echo(f"\nPreferences: (empty)")
        
        # Session State
        session = context.get("session_state", {})
        if session:
            click.echo(f"\nSession State:")
            for key, val in session.items():
                click.echo(f"  - {key}: {val}")
        else:
            click.echo(f"\nSession State: (empty)")
        
        # Last Action
        last_action = context.get("last_action")
        if last_action:
            click.echo(f"\nLast Action: {last_action.get('name')} @ {last_action.get('timestamp')}")
        else:
            click.echo(f"\nLast Action: (none)")
        
        click.echo("=" * 60)
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


@cli.command()
@click.option('--user-id', type=int, required=True, help='User ID')
def clear(user_id):
    """Clear chat context untuk specific user"""
    try:
        mem = RedisMemoryManager(host=redis_host, port=redis_port)
        
        if click.confirm(f"Clear chat context for user {user_id}?"):
            mem.clear_context(user_id)
            click.echo(f"✓ Chat context cleared for user {user_id}")
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


@cli.command()
def clear_all():
    """Clear ALL chat contexts (WARNING: destructive)"""
    try:
        if click.confirm("⚠️  Clear ALL chat contexts? This cannot be undone!"):
            mem = RedisMemoryManager(host=redis_host, port=redis_port)
            mem.clear_all()
            click.echo("✓ All chat contexts cleared")
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


@cli.command()
def list_users():
    """List all users with chat contexts"""
    try:
        r = redis.Redis(host=redis_host, port=redis_port, decode_responses=True)
        keys = r.keys("chat_context:*")
        
        if not keys:
            click.echo("No chat contexts found")
            return
        
        click.echo(f"\n📊 Chat Contexts ({len(keys)} total)")
        click.echo("=" * 60)
        
        for key in sorted(keys):
            user_id = key.split(":")[1]
            context = json.loads(r.get(key))
            messages = context.get("messages", [])
            last_msg_time = messages[-1].get("timestamp", "N/A")[:19] if messages else "N/A"
            
            click.echo(f"  User {user_id:5} | Messages: {len(messages):3} | Last: {last_msg_time}")
        
        click.echo("=" * 60)
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


@cli.command()
@click.option('--user-id', type=int, required=True, help='User ID')
@click.option('--limit', type=int, default=10, help='Number of messages (default: 10)')
def export(user_id, limit):
    """Export chat history untuk user sebagai JSON"""
    try:
        mem = RedisMemoryManager(host=redis_host, port=redis_port)
        context = mem.load_context(user_id)
        
        messages = context.get("messages", [])[-limit:]
        
        export_data = {
            "user_id": user_id,
            "exported_at": datetime.now().isoformat(),
            "message_count": len(messages),
            "preferences": context.get("preferences", {}),
            "session_state": context.get("session_state", {}),
            "messages": messages
        }
        
        filename = f"user_{user_id}_export_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(export_data, f, ensure_ascii=False, indent=2)
        
        click.echo(f"✓ Exported to {filename}")
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


@cli.command()
def monitor():
    """Monitor Redis real-time (press Ctrl+C to stop)"""
    try:
        mem = RedisMemoryManager(host=redis_host, port=redis_port)
        
        click.echo("📡 Redis Monitor (Ctrl+C to stop)")
        click.echo("=" * 60)
        
        import time
        while True:
            stats = mem.get_stats()
            time.sleep(2)
            
            # Clear screen (works on Windows and Unix)
            os.system('clear' if os.name != 'nt' else 'cls')
            
            click.echo("📡 Redis Monitor")
            click.echo("=" * 60)
            click.echo(f"Total Keys: {stats.get('total_keys', 0)}")
            click.echo(f"Used Memory: {stats.get('used_memory', 'N/A')}")
            click.echo(f"Connected Clients: {stats.get('connected_clients', 0)}")
            click.echo(f"Uptime: {stats.get('uptime', 'N/A')}")
            click.echo(f"Operations/sec: {stats.get('operations_per_sec', 0)}")
            click.echo(f"\nUpdated at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    except KeyboardInterrupt:
        click.echo("\n\nMonitor stopped")
    except Exception as e:
        click.echo(f"✗ Error: {e}", err=True)


if __name__ == '__main__':
    cli()
