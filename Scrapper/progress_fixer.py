import os.path
import json
from Utils import replace_forbidden_characters

with open("Data/Series.json", "r") as fp:
    series = json.load(fp)

progress = {}
length = len(series.keys())
count = 0
for key in series.keys():
    print(str(float(count)/float(length)*100)+"%")
    name = replace_forbidden_characters(key)
    path = "Data/series/" + name + "/" + name + ".json"
    if os.path.isfile(path):
        progress[key] = ""
    count += 1

with open("progress.json", "w") as fp:
    json.dump(progress, fp)
