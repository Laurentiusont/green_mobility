def main():
    # int js
    js = int(input('Jumlah Suku: '))
    # int awl
    awl = int(input('Awal: '))
    # int ink
    ink = int(input('Increment: '))
    # string opr
    opr = input('Operasi: ')
    if(opr=='+'):
        # int hsl
        hsl=0
        for i in range(1,js+1,1):
             print(awl,'+',end=' ')
             hsl+=awl
             awl +=ink
        print()
        print('Jumlah Deret: ',hsl)
    if(opr=='*'):
        hsl=1
        for i in range(1,js+1,1):
            print(awl,'*',end=' ')
            hsl*=awl
            awl+=ink
        print()
        print('Hasil Kali: ',hsl)
        
    if(opr=='-'):
        hsl=awl*2
        for i in range(1,js+1,1):
             print(awl,'-',end=' ')
             hsl-=awl
             awl +=ink
        print()
        print('jumlah deret: ',hsl)
main()