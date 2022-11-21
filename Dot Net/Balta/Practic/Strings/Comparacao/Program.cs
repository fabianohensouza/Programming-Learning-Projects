using System;

namespace Comparacao // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = "Testing";

            Console.WriteLine(text.CompareTo("Testing")); //0
            Console.WriteLine(text.CompareTo("testing")); //1


            var text2 = "This text is a test";

            Console.WriteLine(text2.Contains("test")); //True
            Console.WriteLine(text2.Contains("Test")); //False
            Console.WriteLine(text2.Contains("Test", StringComparison.OrdinalIgnoreCase)); //True
        }
    }
}