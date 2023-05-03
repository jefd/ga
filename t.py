f = open('new_users2.csv')

for line in f:
    line = line.strip()
    if not line:
        continue

    date, count = line.split(',')
    month, day, year = date.split('/')
    if len(month) == 1:
        month = '0' + month
    if len(day) == 1:
        day = '0' + day
    year = f'20{year}'
    iso_date = f'{year}-{month}-{day}'
    print(f'{iso_date},{count}')

