using System;

namespace EditorHtml
{

    public static class Menu
    {
        private static int columns;
        private static int lines;

        public static void Show()
        {
            columns = 30;
            lines = 10;

            Console.Clear();
            Console.BackgroundColor = ConsoleColor.Blue;
            Console.ForegroundColor = ConsoleColor.Black;

            DrawScreen();

        }

        public static void DrawScreen()
        {
            DrawUpDown();

            DrawEmptyLines();

            DrawUpDown();

            WriteOptions();
        }

        public static void DrawUpDown()
        {
            Console.Write("+");
            for (int i = 0; i <= columns; i++)
                Console.Write("-");

            Console.Write("+");
            Console.Write("\n");
        }

        public static void DrawEmptyLines()
        {
            for (int line = 0; line <= lines; line++)
            {
                Console.Write("|");

                for (int i = 0; i <= columns; i++)
                    Console.Write(" ");

                Console.Write("|");
                Console.Write("\n");
            }
        }

        public static void WriteOptions()
        {
            Console.SetCursorPosition(3, 2);
            Console.WriteLine("EditorHTML");
        }

    }
}