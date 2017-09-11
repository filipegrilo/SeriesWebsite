import json

def check_links_scrap_completion():
    with open("progress.json", "r") as fp:
        progress = json.load(fp)

    with open("Data/Series.json", "r") as fp:
        series = json.load(fp)

    progress_len = len(progress.keys())
    series_len = len(series.keys())
    print("Links scrapping completion:")
    print("Progress:", str(progress_len), "series")
    print("Total:", str(series_len), "series")
    print("Percentage:", str(int(float(progress_len)/float(series_len)*100)) + "%")

check_links_scrap_completion()

