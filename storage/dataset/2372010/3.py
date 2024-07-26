def main():
    # int n
    n = int(input())
     # int jl
    jl=0
            
    for i in range(1,n+1,1):
        # int prm
        prm = n%i
        if(prm==0):
            jl+=1
    if(jl==2):
        print('Bilangan Prima')
    else:
        print('Bukan Prima')
            
        
main()