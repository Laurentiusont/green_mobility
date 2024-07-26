def main():
    #int n
    n = int(input('angka: '))
    #int jumlah
    jumlah = 0
    
    for i in range(1, n*2 , +1):
        if(n%i==0):
            jumlah=jumlah+1
        
    if jumlah == 2:
        print('prima')
    else:
        print('bukan prima')
    
    
main()