import json
import os.path
import urllib.request
import re

def get_js_generated_page(link, driver=None, return_driver=False):
    if driver == None: driver = init_webdriver()
    driver.get(link)
    html = driver.page_source
    if driver == None and not return_driver: driver.close()
    if return_driver: return driver, html
    return html

def get_config():
    with open("../Config/Config.json") as fp:
        config = json.load(fp)
    return config

def get_links():
    with open(config["paths"]["links"], "r") as fp:
        links = json.load(fp)
    return links

def fix_series():
    with open(config["paths"]["backup"], "r") as fp:
        series = json.load(fp)
    with open(config["paths"]["series_file"], "w") as fp:
        json.dump(series,fp)

def get_char_translations():
    with open(config["paths"]["char_translations"], "r") as fp:
        translations = json.load(fp)
    return translations

def get_imdb_translations():
    with open(config["paths"]["imdb_translations"], "r") as fp:
        translations = json.load(fp)
    return translations

def get_link_translations():
    with open(config["paths"]["link_translations"], "r") as fp:
        translations = json.load(fp)
    return translations

def download_image(url, file, update=False):
    file_splited = file.split("/")
    path = file_splited[:-1]
    name = file_splited[-1]

    path = create_path(path)
    if not is_path_valid(name): name = replace_forbidden_characters(name)

    if not os.path.exists(path+name) or update:
        try:
            urllib.request.urlretrieve(url, path+name)
        except:
            print("404:", url)

def create_path(path, last=""):
    if isinstance(path, str):
        path = path.split("/")

    if len(path) == 0:
        return last

    if not is_path_valid(path[0]): path[0] = replace_forbidden_characters(path[0])

    new = last + path[0] + "/"
    if not os.path.exists(new):
        os.mkdir(new)

    return create_path(path[1:], new)

def is_path_valid(path):
    return re.search("([\\\:\*\?\"<\/>\|])", path) == None

def replace_forbidden_characters(str):
    for key in char_translations.keys():
        str = str.replace(key, char_translations[key])
    return str

def replace_link_characters(str):
    for key in link_translations.keys():
        str = str.replace(key, link_translations[key])
    return str

def print_completion_percentage(cur_step, length, text):
    percent = int(float(cur_step) / float(length) * 100)
    print(text + str(percent) + "%")

config = get_config()
links = get_links()
char_translations = get_char_translations()
imdb_translations = get_imdb_translations()
link_translations = get_link_translations()