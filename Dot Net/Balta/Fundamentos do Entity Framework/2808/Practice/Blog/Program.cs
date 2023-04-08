using System;

namespace Blog
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello World!");

            using (var ctx = new Data.BlogDataContext())
            {

            }
        }
    }
}