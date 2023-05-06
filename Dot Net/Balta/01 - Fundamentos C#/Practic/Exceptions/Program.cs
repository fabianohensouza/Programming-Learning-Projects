using System;

namespace Exceptions // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.Clear();
            var arr = new int[3];

            try
            {
                // for (int i = 0; i < 10; i++)
                // {
                //     Console.WriteLine(arr[i]);
                // }

                Console.WriteLine("Inicio!");

                Salvar("");
                Salvar("Teste");

            }
            catch (IndexOutOfRangeException ex)
            {

                Console.WriteLine(ex.GetType());
                Console.WriteLine(ex.Message);
                Console.WriteLine("Indice não encontrado!");
            }
            catch (ArgumentNullException ex)
            {

                Console.WriteLine(ex.GetType());
                Console.WriteLine(ex.Message);
                Console.WriteLine("Falha ao salvar o texto.");
            }
            catch (MinhaException ex)
            {

                Console.WriteLine(ex.GetType());
                Console.WriteLine(ex.QuandoAconteceu);
                Console.WriteLine("Excessão customizada.");
            }
            catch (Exception ex)
            {

                Console.WriteLine(ex.GetType());
                Console.WriteLine(ex.Message);
                Console.WriteLine("Ops, algo deu errado!");
            }
            finally
            {
                Console.WriteLine("Chegou ao Fim!");
            }


        }

        private static void Salvar(string texto)
        {
            if (string.IsNullOrEmpty(texto))
                throw new MinhaException(DateTime.Now);

            Console.WriteLine(texto);
        }

        public class MinhaException : Exception
        {
            public DateTime QuandoAconteceu { get; set; }

            public MinhaException(DateTime date)
            {
                QuandoAconteceu = date;
            }
        }
    }
}