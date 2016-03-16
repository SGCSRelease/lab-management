from csv import DictReader

spots = """2 mon 2 1 2
9 tue 2 1 2
16 wed 2 1 2
23 thu 2 1 2
30 fri 2 2 2
3 mon 3 2 2
10 tue 3 2 2
17 wed 3 2 2
24 thu 3 2 2
31 fri 3 2 2
4 mon 4 2 2
11 tue 4 2 2
18 wed 4 2 2
25 thu 4 2 2
32 fri 4 2 2
5 mon 5 1 2
12 tue 5 1 2
19 wed 5 1 2
26 thu 5 1 2
33 fri 5 2 2
6 mon 6 1 2
13 tue 6 1 2
20 wed 6 1 2
27 thu 6 1 2
34 fri 6 2 2
7 mon 7 1 2
14 tue 7 1 2
21 wed 7 1 2
28 thu 7 1 2
35 fri 7 2 2""".split("\n")

users = {}
with open("user_list.csv") as csvfile:
    reader = DictReader(csvfile)
    for row in reader:
        users[int(row['ul_id'])] = row['user_name']

managers = open("manager.txt").read().split()


applies = {}
cnt_applies = 0
max_prefer = 0
with open("apply_list.csv") as csvfile:
    reader = DictReader(csvfile)
    for row in reader:
        if row['is_can_mng'] == '1':
            my_id = int(row['ul_id'])
            cnt_applies += 1
            try:
                my_prefer = int(row['prefer_order'])
            except ValueError:
                my_prefer = 31
                print(users[my_id] + " isn't set prefer-order")
            if max_prefer < my_prefer:
                max_prefer = my_prefer
            s = (int(row['mng_id']), my_prefer)
            
            if my_id in applies:
                applies[my_id].append(s)
            else:
                applies[my_id] = [s]

for i in users:
    if i not in applies:
        print(users[i] + " isn't answered at all!")

with open("data.txt", "w") as data:
    data.writelines(str(len(spots)) + '\n')
    for i in spots:
        data.writelines(i + '\n')
    data.writelines('\n')
    
    data.writelines(str(len(users)) + '\n')
    for idx, key in enumerate(users):
        #if not users[key] in managers:
        data.writelines('%s %s %s 2\n' % (str(idx + 1), users[key], key))
    data.writelines('\n')
    
    data.writelines(str(cnt_applies) + " " + str(max_prefer) + "\n")
    for i in applies:
        for j in applies[i]:
            data.writelines("%s %s %s\n" % (list(users.keys()).index(i) + 1, j[0], j[1]))
