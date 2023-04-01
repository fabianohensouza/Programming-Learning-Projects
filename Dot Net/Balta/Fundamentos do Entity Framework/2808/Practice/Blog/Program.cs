using System;

namespace Blog
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello World!");

            var ctx = new Data.BlogDataContext();
        }
    }
}