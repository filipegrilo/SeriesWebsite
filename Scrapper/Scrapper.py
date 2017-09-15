from bs4 import BeautifulSoup
import json
import re
import os.path
import threading
import math
import Utils
import shutil
import requests
import urllib
from datetime import datetime, timedelta
from Utils import links, config, imdb_translations, char_translations

def get_providers():
    providers = {}
    if os.path.exists(config["paths"]["providers"]):
        with open(config["paths"]["providers"], "r") as fp:
            providers = json.load(fp)
    return providers

def get_progress(num_files):
    for i in range(num_files):
        try:
            with open(config["paths"]["progress"].replace("(num)", str(i)), "r") as fp:
                progress = json.load(fp)
                return progress
        except:
            continue

    return None

def clean_up_series(series):
    removed = []

    def remove_series(targets):
        step = 0
        length = len(targets)
        for target in targets:
            Utils.print_completion_percentage(step, length, "Removing:")
            step += 1
            del series_dict[target]
            name = Utils.replace_forbidden_characters(key)
            path = config["paths"]["series_data"].replace("(title)", name)
            if os.path.isfile(path):
                shutil.rmtree(path)

    step = 0
    length = len(series.keys())
    for key in series.keys():
        Utils.print_completion_percentage(step, length, "Creating List: ")
        step += 1
        name = Utils.replace_forbidden_characters(key)
        if not os.path.isfile(config["paths"]["series_data"].replace("(title)", name)+"/"+name+".json"):
            removed.append(key)
            continue
        with open(config["paths"]["series_data"].replace("(title)", name)+"/"+name+".json") as fp:
            try:
                result = json.load(fp)
            except:
                print(name)
        if len(result["seasons"]) == 0:
            removed.append(key)
    remove_series(removed)
    with open(config["paths"]["series_file"], "w") as fp:
        json.dump(series_dict, fp)

    return removed

def add_provider(provider_name, provider_img_url):
    if not provider_name in providers:
        img_path = config["paths"]["providers_images"].replace("(name)", provider_name)
        providers[provider_name] = {"name": provider_name, "img_path": img_path}
        Utils.download_image(provider_img_url, img_path)
        with open(config["paths"]["providers"], "w") as fp:
            json.dump(providers, fp)
    return providers[provider_name]

def get_series(update=False, num_threads=1, verbose=True):
    error = config["errors"]["myseries_404"]
    thread_lock = threading.Lock()
    path = config["paths"]["series_file"]
    series_dict = {}

    def scrap_series(start_index, length, text):
        driver, html = Utils.get_js_generated_page(links["main"]["series"], return_driver=True)
        soup = BeautifulSoup(html, config["consts"]["parser"])
        step = start_index
        last_index = start_index + length

        while soup.head.title.text != error and step < last_index:
            if verbose:
                Utils.print_completion_percentage(step-start_index, length, text)

            series_soup = soup.find_all("li", "category-item episode_not_watched")
            for series in series_soup:
                title = series.a["title"]
                try:
                    year = re.search("\((.+)\)", series.a.text).group(1)
                except:
                    year = "null"
                description = series.find("div", "info").div.text
                img_url = series.find("img")["src"]
                Utils.download_image(img_url, config["paths"]["series_image"].replace("(title)", title) + title + ".jpg")
                thread_lock.acquire()
                series_dict[title] = {"title": title, "year": year, "description": description}
                thread_lock.release()

            if step % 50 == 0:
                thread_lock.acquire()
                series_dict["last_mod"] = {"step": step, "series": title}
                with open(path, "w") as fp:
                    json.dump(series_dict, fp)
                thread_lock.release()

            step += 1
            html = Utils.get_js_generated_page(links["main"]["series"] + str(step), driver)
            soup = BeautifulSoup(html, config["consts"]["parser"])

    def update_series():
        num_pages = config["consts"]["myseries_num_pages"]
        threads = []
        length = math.ceil(num_pages / num_threads)

        for i in range(num_threads):
            start_index = i * length + 1
            t = threading.Thread(target=scrap_series, args=(start_index, length, "Thread" + str(i) + ": "))
            t.start()
            threads.append(t)

        for t in threads:
            t.join()

        with open(path, "w") as fp:
            json.dump(series_dict, fp)

    if not os.path.exists(path) or update:
        Utils.create_path(path.split("/")[:-1])
        update_series()
    else:
        with open(path,"r") as fp:
            series_dict = json.load(fp)

    return series_dict

def get_all_series_seasons(series, num_threads=1, verbose=True, cur_iteration=None, update=False):
    threads = []
    thread_lock = threading.Lock()

    def load_progress():
        if cur_iteration != None and not update:
            for k in cur_iteration.keys():
                if k in series:
                    del series[k]

    def scrap_series(series, length, text):
        step = 0
        for s in series:
            if verbose:
                Utils.print_completion_percentage(step, length, text)
                step += 1
            if cur_iteration != None and s in cur_iteration:
                continue
            get_series_seasons(s, thread_lock=thread_lock)

    length = math.ceil(len(series.keys()) / num_threads)
    l = list(series.keys())
    for i in range(num_threads):
        start_index = i * length
        last_index = start_index + length
        t = threading.Thread(target=scrap_series, args=(l[start_index:last_index], length, "Thread" + str(i) + ": "))
        t.start()
        threads.append(t)

    for t in threads:
        t.join()

def scrap_links(url, return_episode_info=False):

    def get_episode_info():
        date_str = soup.find("div", {"itemprop": "episode"}).find("p", {"style": "margin-bottom:5px;"}).text.split(":")[-1]
        date = datetime.strptime(date_str, " %b %d, %Y")
        date_str = date.strftime("%Y-%m-%d")
        episode_info_soup = soup.find("h1", "channel-title").span
        series_name = episode_info_soup.a.span.text
        span_tag = episode_info_soup.find("span", "list-top")
        season = span_tag.a.text.split(" ")[-1]
        episode_num = span_tag.text.split("-")[0][1:-1].split(" ")[-1]
        episode_name = (''.join(span_tag.text.split("-")[1:]))[1:]
        episode_info = {"series_name": series_name, "season": season, "episode_num": episode_num,
                        "episode_name": episode_name, "date": date_str}
        return episode_info

    html = requests.get(url).text
    soup = BeautifulSoup(html, config["consts"]["parser"])

    try:
        links_soup = soup.find(id="linktable").table
    except:
        return scrap_links(soup.center.a["href"], return_episode_info=return_episode_info)
    if links_soup == None:
        if return_episode_info:
            return get_episode_info(), []
        return []
    links_soup = links_soup.tbody.find_all("tr")

    links_list = []
    for link_soup in links_soup:
        if link_soup["class"][0] == "download_link_sponsored": continue
        provider_img_url = link_soup.find("span", "host").img["src"]
        provider_name = link_soup.find("span", "host").text.replace("\xa0", "")
        provider = add_provider(provider_name, provider_img_url)
        link = link_soup.find("td", "deletelinks").a["onclick"]
        try:
            link = re.search("(http|https)://([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:/~+#-]*[\w@?^=%&/~+#-])?",link).group(0)
        except:
            continue
        links_list.append({"provider": provider, "link": link})

    if return_episode_info:
        return get_episode_info(), links_list

    return links_list

def get_series_url(series_name):
    name_parsed = urllib.parse.quote(series_name, encoding="raw_unicode_escape")
    url = links["main"]["search"]+name_parsed
    try:
        html = requests.get(url).text
    except:
        return get_series_url(series_name)
    soup = BeautifulSoup(html, config["consts"]["parser"])

    options = soup.find_all("div", "search-item-left")

    for option in options:
        series_url = option.find("div", {"style": "padding-left: 10px;"}).a["href"]
        title = option.find("div", {"style": "padding-left: 10px;"}).a.text
        regex = re.search("\(([0-9]+)\)", title)
        try:
            year = regex.group(len(regex.groups()))
            if series_dict[series_name]["year"] == year:
                return series_url
        except:
            return series_url

    return None

def get_series_seasons(series_name, update=False, thread_lock=None, thread_num=0, verbose=False):
    original_name = series_name
    if not Utils.is_path_valid(series_name):
        series_name = Utils.replace_forbidden_characters(series_name)
    path = Utils.create_path(config["paths"]["series_data"].replace("(title)", series_name))
    path += series_name+".json"

    def scrap():
        url = get_series_url(original_name)
        if url == None:
            return
        html = requests.get(url).text
        soup = BeautifulSoup(html, config["consts"]["parser"])

        seasons_soup = soup.find_all("div", {"itemprop": "season"})
        seasons = []
        for season in seasons_soup:
            season_num = season.h2.a.span.text.split(" ")[-1]
            episodes_soup = season.ul.find_all("li")
            episodes = []
            for episode in episodes_soup:
                episode_num = episode.find("meta", {"itemprop": "episodenumber"})["content"]
                links_url = episode.find("meta", {"itemprop": "url"})["content"]
                episode_name = episode.a.find("span", {"itemprop": "name"}).text.split("\xa0")[-1]
                episode_date = episode.a.find("span", "epnum").span.text
                num_links = re.search("\((.*) ", str(episode.a.find("span", "epnum").b)).group(1)
                if num_links == "0":
                    links_list = []
                else:
                    try:
                        links_list = scrap_links(links_url)
                    except:
                        print(links_url)
                episodes.append({"number": episode_num, "name": episode_name, "date": episode_date, "links": links_list})
                if verbose:
                    print("Season (" + season_num + "/" + str(len(seasons_soup)) + "): Episode (" + episode_num + "/" + str(len(episodes_soup)) + ")")
            episodes.sort(key=lambda episode: int(episode["number"]))
            seasons.append({"number": season_num, "episodes": episodes})
        seasons.sort(key=lambda season: int(season["number"]))
        seasons_dict = {"seasons":seasons}
        with open(path, "w") as fp:
            json.dump(seasons_dict, fp)

        if thread_lock != None:
            thread_lock.acquire()
        progress[series_name] = ""
        with open(config["paths"]["progress"].replace("(num)", str(thread_num)), "w") as fp:
            json.dump(progress, fp)
        if thread_lock != None:
            thread_lock.release()
        return seasons_dict

    if not os.path.exists(path) or update:
        seasons_dict = scrap()
    else:
        with open(path,"r") as fp:
            seasons_dict = json.load(fp)

    return seasons_dict

def get_series_info(series_name, update=False, thread_lock=None, thread_num=0):

    def get_best_result(results):
        best = None
        best_score = 10000 #arbitry number bigger than any resonable score
        for result in results:
            try:
                year = int(series_dict[series_name]["year"])
            except:
                year = 0
            score = abs(year-result["year"])
            if score < best_score:
                best_score = score
                best = result
        return best

    def process_imdb_link(name):
        for key in imdb_translations.keys():
            name = name.replace(key, imdb_translations[key])
        return name

    def generate_null_info():
        info = {"rating": None, "genres": None}
        return info

    def save_progress(info):
        if thread_lock != None:
            thread_lock.acquire()

        progress[series_name] = info
        with open(config["paths"]["progress"].replace("(num)", str(thread_num)), "w") as fp:
            json.dump(progress.copy(), fp)

        if thread_lock != None:
            thread_lock.release()

    def scrap():
        try:
            html = requests.get(links["imdb"]["search"].replace("(name)", process_imdb_link(series_name))).text
        except:
            scrap()
        soup = BeautifulSoup(html, config["consts"]["parser"])
        try:
            results_soup = soup.find("table", "findList").tbody.find_all("tr")
        except:
            return generate_null_info()

        results = []
        for result in results_soup:
            part = result.find("td", "result_text")
            result_name = part.a.text
            result_href = part.a["href"]
            try:
                result_year = re.search("\(([0-9]+)\)", part.text).group(1)
            except:
                continue
            result = {"name": result_name, "year": int(result_year),"href": result_href}
            results.append(result)
        best_result = get_best_result(results)

        if best_result == None:
            return generate_null_info()

        html = requests.get(links["imdb"]["root"]+best_result["href"]).text
        soup = BeautifulSoup(html, config["consts"]["parser"])
        try:
            rating = soup.find("span", {"itemprop": "ratingValue"}).text
        except:
            rating = "None"
        genres = [genre.span.text for genre in soup.find("div", "subtext").find_all("a")[:-1]]
        info = {"rating": rating, "genres": genres}
        save_progress(info)

        return info

    if update or not "info" in series_dict[series_name]:
        info = scrap()
    else:
        info = series_dict[series_name]["info"]

    return info

def get_all_series_info(series, num_threads=1, verbose=True, cur_iteration=None, update=False):
    threads = []
    thread_lock = threading.Lock()

    def add_info_to_series():
        def scrap(l, length, text, num):
            step = 0
            for s in l:
                if verbose:
                    Utils.print_completion_percentage(step, length, text)
                    step += 1
                try:
                    scrap_series_from_name(s, thread_lock)
                except:
                    scrap_series_from_name(s, thread_lock)


        series_to_scrap = []

        for key in list(progress):
            try:
                series_dict[key]["info"] = progress[key]
            except:
                series_to_scrap.append(key)
                continue

        length = math.ceil(len(series_to_scrap) / num_threads)
        l = series_to_scrap
        for i in range(num_threads):
            start_index = i * length
            last_index = start_index + length
            t = threading.Thread(target=scrap,
                                 args=(l[start_index:last_index], length, "Thread" + str(i) + ": ", i))
            t.start()
            threads.append(t)

        for t in threads:
            t.join()



        with open(config["paths"]["series_file"], "w") as fp:
            json.dump(series_dict, fp)

    def load_progress():
        if cur_iteration != None and not update:
            for k in cur_iteration.keys():
                if k in series:
                    del series[k]

    def scrap_series(series, length, text, num):
        step = 0
        for s in series:
            if verbose:
                Utils.print_completion_percentage(step, length, text)
                step += 1
            get_series_info(s, update=True, thread_lock=thread_lock, thread_num=num)

    load_progress()
    length = math.ceil(len(series.keys()) / num_threads)
    l = list(series.keys())
    for i in range(num_threads):
        start_index = i * length
        last_index = start_index + length
        t = threading.Thread(target=scrap_series, args=(l[start_index:last_index], length, "Thread" + str(i) + ": ", i))
        t.start()
        threads.append(t)

    for t in threads:
        t.join()

    add_info_to_series()

def scrap_series_and_seasons(series_url, thread_lock=None, update=False):
    if series_url == None:
        return
    html = requests.get(series_url).text
    soup = BeautifulSoup(html, config["consts"]["parser"])
    name = soup.find("h1", "channel-title").a.span.text
    img_url = soup.find("img", {"itemprop": "image"})["src"]
    Utils.download_image(img_url, config["paths"]["series_image"].replace("(title)", name) + name + ".jpg")
    try:
        year = soup.find("span", {"itemprop": "startDate"}).a.text
    except:
        year = None
    description = (soup.find("div", "show-summary").p.find_all("strong")[-1]).next_sibling
    series_dict[name] = {"title": name, "year": year, "description": description}
    get_series_seasons(name, verbose=True, update=update)
    get_series_info(name, update=True)
    if thread_lock != None:
        thread_lock.acquire()
    with open(config["paths"]["series_file"], "w") as fp:
        json.dump(series_dict.copy(), fp)
    if thread_lock != None:
        thread_lock.release()

def scrap_series_from_name(series_name, thread_lock=None, update=False):
    print("Downloading ("+series_name+")...")
    url = get_series_url(series_name)
    scrap_series_and_seasons(url, thread_lock, update)

def scrap_series_from_episode_url(episode_url):
    html = requests.get(episode_url).text
    soup = BeautifulSoup(html, config["consts"]["parser"])
    series_url = soup.find("h1", "channel-title").find("a", {"itemprop": "url"})["href"]
    scrap_series_and_seasons(series_url)

def get_new_episodes(num_threads=1, verbose=True):
    html = requests.get(links["main"]["new_episodes"]).text
    soup = BeautifulSoup(html, config["consts"]["parser"])
    last_date = None
    new_episodes = {"last_update": datetime.now().strftime("%d-%m-%Y %H:%M:%S"), "episodes": []}

    day_delta = config["consts"]["day_delta"]
    for day_soup in soup.find_all("div", "slide"):
        day_str = day_soup.find("ul", "tabs").li.a.div.text

        if day_str == "Yesterday" or day_str == "Today":
            date = last_date + timedelta(days=1)
        else:
            date = datetime.strptime(day_str[:-2], "%a, %b %d")
            date = date.replace(year=(datetime.now() - timedelta(days=day_delta)).year)
        day_delta -= 1
        date_str = date.strftime("%d-%m-%Y")
        day_episodes = []
        day_series = day_soup.find("ul", "listings").find_all("li")
        for series_soup in day_series:
            url = series_soup.find("a",{"target": "_blank"})["href"]
            episode_info, episode_links = scrap_links(url, return_episode_info=True)
            if verbose:
                print(episode_info["series_name"] + ": Season " + episode_info["season"] + " Episode " + episode_info["episode_num"])
            original_name = episode_info["series_name"]
            if not original_name in series_dict:
                print('No data for "' + original_name + '": downloading...')
                scrap_series_from_episode_url(url)
                continue

            if not Utils.is_path_valid(episode_info["series_name"]):
                episode_info["series_name"] = Utils.replace_forbidden_characters(episode_info["series_name"])
            path = Utils.create_path(config["paths"]["series_data"].replace("(title)", episode_info["series_name"]))
            path += episode_info["series_name"] + ".json"

            try:
                with open(path, "r") as fp:
                    series = json.load(fp)
            except:
                continue

            if len(series["seasons"]) < int(episode_info["season"]):
                d = {"number": episode_info["season"], "episodes": []}
                series["seasons"].append(d)

            d = {"name": episode_info["episode_name"],
                 "number": episode_info["episode_num"],
                 "date": episode_info["date"],
                 "links": episode_links
                 }

            try:
                series["seasons"][int(episode_info["season"]) - 1]["episodes"]
            except:
                print("Series badly downloaded("+original_name+"): downloading again")
                get_series_seasons(original_name, update=True, verbose=True)
                with open(path, "r") as fp:
                    series = json.load(fp)

            if len(series["seasons"][int(episode_info["season"])-1]["episodes"]) < int(episode_info["episode_num"]):
                series["seasons"][int(episode_info["season"])-1]["episodes"].append(d)
            else:
                series["seasons"][int(episode_info["season"])-1]["episodes"][int(episode_info["episode_num"])-1] = d

            day_episodes.append({"series": original_name, "season": episode_info["season"], "episode": episode_info["episode_num"]})

            try:
                with open(path, "w") as fp:
                    json.dump(series, fp)
            except:
                print("Error saving series!")
                continue
        new_episodes["episodes"].append({"date": date_str, "day_episodes": day_episodes})
        last_date = date

        if day_str == "Today": break

    new_episodes["episodes"] = list(reversed(new_episodes["episodes"]))
    print("Saving new episodes...")
    with open(config["paths"]["new_episodes"], "w") as fp:
        json.dump(new_episodes, fp)

def save_backup():
    with open(config["paths"]["backup"], "w") as fp:
        json.dump(series_dict, fp)

def fix_series_json():
    files = os.listdir("../Data/Series/")
    for file in files:
        for key in char_translations.keys():
            file = file.replace(char_translations[key], key)
        if not file in series_dict:
            scrap_series_from_name(file)

def scrap_series_from_file():
    with open(config["paths"]["series_to_scrap"], "r") as fp:
        series_to_scrap = json.load(fp)

    for series in series_to_scrap["series"]:
        scrap_series_from_name(series, update=True)

    series_to_scrap["series"] = []

    with open(config["paths"]["series_to_scrap"], "w") as fp:
        json.dump(series_to_scrap,fp)


num_threads = config["consts"]["num_threads"]
on = config["consts"]["run"]

print("Fixing series with backup file...")
Utils.fix_series()
print("Loading progress...")
progress = get_progress(num_threads)
print("Loading progress...")
providers = get_providers()
print("Loading series...")
series_dict = get_series()

if on:
    print("Scrapping series from file...")
    scrap_series_from_file()
    print("Downloading latest episodes...")
    get_new_episodes()
    print("Saving series backup...")
    save_backup()
