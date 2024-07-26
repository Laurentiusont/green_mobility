def main():
    # int n 
    n = int(input())
        
    if n > 1:
        if n % 3 != 0 and n % 2 != 0:
            print('prima')
        else:
            print('bukan prima') 
    else:
        print('bukan prima')
main()