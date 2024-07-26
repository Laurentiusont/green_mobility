def main():
    #int n
    n = int(input("N = "))
    if (n<= 1):
        print ("Bukan Prima")
    else:
        is_prime = True
        for i in range(2,n):
            if(n % 1 ==0):
             is_prime = False
             break
    if(is_prime):
        print('prima')
    else: 
        print('bukan prima')
if __name__ == '__main__':
    
 main()