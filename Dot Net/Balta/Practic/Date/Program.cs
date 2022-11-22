using System;

namespace Date // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.Clear();
            //var data = new DateTime();

            //var data = new DateTime(2020, 10, 12, 8, 23, 14);
            var data = DateTime.Now;
            var formatada = String.Format("{0:d}/{0:MM}/{0:yyyy}.", data);
            var formatada2 = String.Format("{0:R}.", data);

            //Console.WriteLine(formatada);
            //Console.WriteLine(formatada2);
            //Console.WriteLine(data.Year);
            //Console.WriteLine(data.DayOfWeek);
            //Console.WriteLine((int)data.DayOfWeek);
            Console.WriteLine(data.AddDays(900));
            Console.WriteLine(data.AddMonths(900));
            Console.WriteLine(data.AddYears(900));
        }
    }
}