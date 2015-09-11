from copy import deepcopy
from random import randint
from random import shuffle
from sys import argv
import timeit

MAX_TIME_PER_CALC = 10

def error():
    print('Usage: [ -e / -r [number of search] ]')
    print('-e : exhaustive search')
    print('-r : random search')
    exit()

# ignoring null string from input
def myinput():
    try:
        s = input()
        while not s:
            s = input()
        return s
    except EOFError:
        return ""

if len(argv) < 2:
    error()

if not ((argv[1]=='-r' and len(argv)==3) or (argv[1]=='-e' and len(argv)==2)):
    error()

if argv[1]=='-r':
    try:
        int(argv[2])
    except:
        error()

# class input
# class_dict[idx of server] = (day of the week, period, weight of period, total person managing)
total_class = int(myinput())
class_dict = {}
for i in range(total_class):
    s = myinput().split()
    class_dict[int(s[0])] = list((s[1],s[2],int(s[3]),int(s[4])))

# person input
# person_dict[idx of server] = (name, chosen number of sessions, mandatory sessions)
total_person = int(myinput())
person_dict = {}
for i in range(total_person):
    s = myinput().split()
    person_dict[int(s[0])] = list((s[1],int(s[2]),int(s[3])))
    # 2 meaning person s[0] need to do 2 session

# graph input
# edge_list[class] = person1,person2,...
# weight_list[(class,person)] = w = total number - priority rank number
total_edge = int(myinput().split()[0])
edge_list = {}
weight_list = {}
for i in range(total_edge):
    s = [int(i) for i in myinput().split()]
    if not edge_list.get(s[1]): edge_list[s[1]] = list()
    edge_list[s[1]].append(s[0])
    weight_list[(s[1],s[0])] = person_dict[s[0]][1] - s[2]

# edge_key_list sorted by length (shorter class is searched first)
edge_key_list = sorted(edge_list.keys(),key = lambda x:len(edge_list[x]))
# each edge_list sorted by priority of each person
for i in edge_list: edge_list[i].sort(key = lambda x:(-weight_list[(i,x)],-person_dict[x][1]))

# only searches for one answer
# brute force algorithm
# returns as dict[class] = person
def dfs(curr=0):
    if timeit.default_timer() - tic > MAX_TIME_PER_CALC:
        raise
    if curr == total_class: return True
    res = dict()
    k = edge_key_list[curr]
    for i in edge_list[k]:
        if person_dict[i][2] >= class_dict[k][2]:
            person_dict[i][2] -= class_dict[k][2]
            tmp = dfs(curr+1)
            person_dict[i][2] += class_dict[k][2]
            if tmp:
                if type(tmp)==bool: tmp = dict()
                if len(tmp)+1 > len(res):
                    tmp[k] = i
                    res = tmp
                if len(res) == total_class - curr:
                    return res
    tmp = dfs(curr+1)
    if type(tmp)==dict and len(tmp)>len(res): res = tmp
    return res

# search for every answer
# returns list of answers
def exhaustdfs(curr=0,stk={},ans=[]):
    if curr==total_class:
        ans.append(deepcopy(stk))
        return ans
    k = edge_key_list[curr]
    for i in edge_list[k]:
        if person_dict[i][2] >= class_dict[k][2]:
            person_dict[i][2] -= class_dict[k][2]
            stk[k] = i
            tmp = exhaustdfs(curr+1,stk,ans)
            person_dict[i][2] += class_dict[k][2]
            if tmp: ans = tmp
            del stk[k]
    return ans

max_weight = 0
max_list = []

# list of answers
essential_list = list()
if argv[1]=='-e':
    essential_list = exhaustdfs()
elif argv[1]=='-r':
    for t in range(int(argv[2])):
        edge_key_list = sorted(edge_list.keys(),key = lambda x:len(edge_list[x])+randint(-3,3))
        tic = timeit.default_timer()
        tmp_dict = person_dict
        try:
            l = dfs()
            if l:
                essential_list.append(l)
                print(l)
        except:
            person_dict = tmp_dict
            pass

for essential in essential_list:
    for i,j in essential.items():
        class_dict[i][3] -= 1
        person_dict[j][2] -= 1
    priority_list = sorted(weight_list.keys(),key=lambda x:-weight_list[x])
    queue = []
    for (i,j) in priority_list:
        if class_dict[i][3] > 0 and person_dict[j][2] > 0 and i in essential.keys() and essential[i] != j:
            queue.append((i,j))
            class_dict[i][3] -= 1
            person_dict[j][2] -= 1
    for i in essential.keys():
        essential[i] = [essential[i]]
    for (i,j) in queue:
        essential[i].append(j)
    
    tmp_weight = 0
    for i in essential.keys():
        for j in essential[i]:
            tmp_weight += weight_list[(i,j)]
        class_dict[i][3] += len(essential[i])
        for j in essential[i]: person_dict[j][2] += 1
    if tmp_weight > max_weight and len(essential) > len(max_list):
        max_weight = tmp_weight
        max_list = essential

for i in max_list.keys():
    print(class_dict[i][0],class_dict[i][1])
    print(','.join(person_dict[j][0] for j in max_list[i]))
