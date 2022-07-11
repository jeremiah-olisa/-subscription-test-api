import os
from dotenv import dotenv_values, load_dotenv

load_dotenv('.env.production')
loadedEnv = dotenv_values('.env.production')

for key in loadedEnv:
    variable = str(os.getenv(key))
    # print(f'heroku config:set {key}={variable}')
    os.system(f'heroku config:set {key}={variable} -a zora-backend')