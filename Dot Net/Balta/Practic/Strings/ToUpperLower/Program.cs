using System;

namespace ToUpperLower // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = "This Text Is a Test";

            Console.WriteLine(text.ToUpper());
            Console.WriteLine(text.ToLower());

            var position = text.IndexOf("Test");

            var text2 = text.Insert(position, "(little) ");
            Console.WriteLine(text2);

            Console.WriteLine(text2.Remove(position, 9));

            Console.WriteLine(text.Length);
            Console.WriteLine(text2.Length);
        }
    }
}
