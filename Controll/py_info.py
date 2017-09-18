from subprocess import Popen,PIPE
import json

with open("../Config/Config.json", "r") as fp:
	config = json.load(fp)

with Popen(["cat", "/sys/class/thermal/thermal_zone0/temp"], stdout=PIPE) as proc:
	cpu_temp = int(proc.stdout.read().decode("utf-8"))
print("<h1>CPU Temp:</h1>", str(cpu_temp/1000), " C")

with Popen(["mpstat", "-P", "ALL"], stdout=PIPE) as proc:
	cpu_usage = proc.stdout.read().decode("utf-8")
lines = cpu_usage.split("\n")
cpu_usage_html = lines[0] + "<table><tr>"
headers = lines[2].split(" ")
for header in headers:
	if header != "":	
		cpu_usage_html += "<th>"+header+"</th>"
cpu_usage_html += "</tr>"
for line in lines[3:]:
	columns = line.split(" ")
	cpu_usage_html += "<tr>"
	for column in columns:
		if column != "":
			cpu_usage_html += "<td>"+column+"</td>"
	cpu_usage_html += "</tr>"
cpu_usage_html += "</table>"		
print("<h1>CPU Usage:</h1>",cpu_usage_html)

with open(config["paths"]["new_episodes"], "r") as fp:
	new_episodes = json.load(fp)
print("<h1>Last Episode Update:</h1>", new_episodes["last_update"])

with Popen(["ps", "-ax"], stdout=PIPE) as proc:
	processes = proc.stdout.read().decode("utf-8")
print("<h1>Processes:</h1>", processes)
