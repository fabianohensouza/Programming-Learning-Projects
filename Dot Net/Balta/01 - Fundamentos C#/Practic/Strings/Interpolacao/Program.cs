using System;

namespace Interpolacao // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var price = 10.2;
            var product = "Television";
            //var text = "The product price is $" + price;
            //var text = string.Format("The {1} price is ${0} only in the hot sale!", price, product);
            //var text = $"The {product} price is ${price} only in the hot sale!";
            //var text = $@"The {product} price is ${price} 
            //only in the hot sale!";
            var text = $"The {product} price is ${price} \nonly in the hot sale!";

            Console.WriteLine(text);
        }
    }
}