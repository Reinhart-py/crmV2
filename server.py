#!/usr/bin/env python3
"""
Rolexto CRM - Instant Local Preview Server
Zero dependencies required. Runs on Python 3.6+.
"""
import sys
import os
import webbrowser
from http.server import HTTPServer, SimpleHTTPRequestHandler

PORT = 8000

class CRMRequestHandler(SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Cache-Control', 'no-cache, no-store, must-revalidate')
        super().end_headers()

def run_server():
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    server_address = ('', PORT)
    httpd = HTTPServer(server_address, CRMRequestHandler)
    url = f"http://localhost:{PORT}/preview.html"
    print("=================================================================")
    print("  [*] ROLEXTO CRM - EXECUTIVE DASHBOARD PREVIEW SERVER")
    print("=================================================================")
    print(f"  Local URL    : {url}")
    print(f"  Directory    : {os.getcwd()}")
    print("  Press Ctrl+C to stop the server.")
    print("=================================================================")
    try:
        webbrowser.open(url)
    except Exception:
        pass
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n[!] Shutting down server gracefully...")
        httpd.server_close()
        sys.exit(0)

if __name__ == '__main__':
    run_server()
