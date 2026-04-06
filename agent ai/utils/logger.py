import datetime

def log_step(title, data=None):

    print("\n" + "="*50)
    print(f"[{datetime.datetime.now()}] {title}")
    print("="*50)

    if data:
        print(data)