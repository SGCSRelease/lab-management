a14 = []
for i in open("14.txt").split():
    a14.append("14"+ i)
a15 = []
for i in open("15.txt").split():
    a15.append("15"+ i)
a16 = []
for i in open("16.txt").split():
    a16.append("16" + i)

aa = "(NULL, '%s', NULL, NULL, '2', '0', '0')"
A = []
for i in a14:
    A.append(aa % i)
for i in a15:
    A.append(aa % i)
for i in a16:
    A.append(aa % i)
AA = ','.join(A)

a = """INSERT INTO `taeguk`.`user_list` (`ul_id`, `user_name`, `user_pw`, `pw_salt`, `mng_time`, `is_exist_pw`, `stage`) VALUES %s;"""
print(a % AA)

