using System;
using System.Threading;

namespace Stopwatch
{
    class Program
    {
        static void Main(string[] args)
        {
            Menu();
        }

        static void Menu()
        {
            Console.Clear();
            Console.WriteLine("S = Contar em segundos");
            Console.WriteLine("M = Contar em Minutos");
            Console.WriteLine("0 = Sair");
            Console.WriteLine("Quanto tempo deseja contar?");

            string data = Console.ReadLine().ToLower();
            char type = char.Parse(data.Substring(data.Length - 1, 1));
            Console.WriteLine(type);
            Console.WriteLine(data);
        }

        static void Start(int time)
        {
            int currentTime = 0;

            while (currentTime < time)
            {
                currentTime++;
                Console.Clear();
                Console.WriteLine(currentTime);
                Thread.Sleep(1000);
            }

            Console.Clear();
            Console.WriteLine("StopWatch Finalizado!");
            Thread.Sleep(2500);
        }
    }
}