import os
import subprocess
import requests
import time
import sys
import json
import time

def dir(directory,token):
    # 检查目录是否存在
    if not os.path.isdir(directory):
        print(f"{directory} is not a directory")
        sys.exit(1)

    # 处理文件
    while True:
        # 查找第一个文件
        first_file = None
        for root, _, files in os.walk(directory):
            if files:
                first_file = os.path.join(root, files[0])
                break
        # 没有文件
        if not first_file:
            print("No more files found")
            time.sleep(1)
            continue
        else:
            print(f"Found file: {first_file}")
            ext = os.path.splitext(first_file)[1].lower()  # 获取文件扩展名并转为小写

            if ext == ".png":
                print(f"{first_file} is a PNG file")
                # 调用 upload.py 脚本处理文件
                upload(first_file,token)
        time.sleep(1)

def upload(file_path,token):
        # 使用 base64 编码文件内容
        try:
            content = subprocess.check_output(["base64", "-w", "0", file_path], text=True)
        except subprocess.CalledProcessError as e:
            print(f"Failed to encode file: {e}")
            return

        # 设置 API 请求参数  # 替换为你的 github token
        now = int(time.time())
        url = f"https://api.github.com/repos/liuxiongzhuo/img/contents/i/{now}.png"
        data = {
            "content": content,
            "message": f"new file {os.path.basename(file_path)}",
            "branch":"main"
        }
        print(data)
        # 发送 PUT 请求
        headers={"Authorization":"Bearer "+token}
        try:
            response = requests.put(url,headers=headers, data=json.dumps(data))
            response.raise_for_status()  # 检查请求是否成功
            print("File uploaded successfully")
        except requests.exceptions.RequestException as e:
            print(f"Failed to upload file: {e}")
            return

        # 删除文件
        try:
            os.remove(file_path)
            print(f"delete {file_path}")
        except OSError as e:
            print(f"Failed to delete file: {e}")
        
    
if len(sys.argv) != 3:
    print("only need dir and token")
    sys.exit(1)
directory = sys.argv[1]
token = sys.argv[2]
dir(directory,token)
