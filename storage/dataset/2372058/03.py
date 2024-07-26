def main ():
    # Int number
    n = int(input('N :'))
    
    jumlah = 0 
    
    for i in range (1, i <= n, i + 1):
        if (n % i == 0):
            jumlah = jumlah + 1 
        elif (jumlah == 2 ):
            print ('prima')
        else:
            print ('Bukan prima')
main ()