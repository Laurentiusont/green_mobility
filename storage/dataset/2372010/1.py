def main():
    # int n
    n = int(input('N: '))
    for i in range(1,n+1,1):
        # int agk
        agk = int(input())
        if(agk%2==0):
            print('Genap')
        elif(agk%2!=0):
            print('Bukan Genap')
main()