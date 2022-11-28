using System;
using System.Globalization;

namespace Moeda // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.Clear();

            decimal value = 15029.69m;
            decimal value2 = 15029.45m;

            Console.WriteLine(value);

            Console.WriteLine(
                value.ToString(
                    "C",
                    CultureInfo.CreateSpecificCulture("pt-BR")
                )
            );

            Console.WriteLine(Math.Round(value));
            Console.WriteLine(Math.Round(value2));

            Console.WriteLine(Math.Ceiling(value));
            Console.WriteLine(Math.Ceiling(value2));

            Console.WriteLine(Math.Floor(value));
            Console.WriteLine(Math.Floor(value2));
        }
    }
}