using System;
using Blog.Models;
using Blog.Repositories;
using Dapper.Contrib.Extensions;
using Microsoft.Data.SqlClient;

namespace Blog // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        private const string CONNECTION_STRING = @"Server=localhost,1433;Database=Blog;User ID=sa;Password=1q2w3e4r@#$;TrustServerCertificate=True";

        static void Main(string[] args)
        {
            var connection = new SqlConnection(CONNECTION_STRING);
            connection.Open();

            //Console.WriteLine("Hello World!");
            //ReadUsers();
            //ReadUser();
            //CreateUser();
            //UpdateUser();
            //DeleteUser();
            ReadUsers(connection);
            ReadRoles(connection);
            ReadTags(connection);

            connection.Close();
        }


        public static void ReadUsers(SqlConnection connection)
        {

            var repository = new Repository<User>(connection);
            var users = repository.Get();

            foreach (var user in users)
                Console.WriteLine(user.Name);
        }


        public static void ReadRoles(SqlConnection connection)
        {

            var repository = new Repository<Role>(connection);
            var items = repository.Get();

            foreach (var item in items)
                Console.WriteLine(item.Name);
        }


        public static void ReadTags(SqlConnection connection)
        {

            var repository = new Repository<Tag>(connection);
            var items = repository.Get();

            foreach (var item in items)
                Console.WriteLine(item.Name);
        }

        public static void ReadUser()
        {
            using (var connection = new SqlConnection(CONNECTION_STRING))
            {
                var user = connection.Get<User>(2);

                Console.WriteLine(user.Name);
            }
        }

        public static void CreateUser()
        {
            var user = new User()
            {
                Name = "Sabrina Souza",
                Email = "sabrina@souza.io",
                Bio = "Estudante Farmacia",
                PasswordHash = "HASH",
                Image = "HTTPS://...",
                Slug = "sabrina-souza"
            };

            using (var connection = new SqlConnection(CONNECTION_STRING))
            {
                var lines = connection.Insert<User>(user);

                Console.WriteLine($"Usuário Cadastrado");
            }
        }

        public static void UpdateUser()
        {
            var user = new User()
            {
                Id = 3,
                Name = "Sabrina Souza dos Santos",
                Email = "sabrina@souza.io",
                Bio = "Farmaceutica",
                PasswordHash = "HASH",
                Image = "HTTPS://...",
                Slug = "sabrina-souza"
            };

            using (var connection = new SqlConnection(CONNECTION_STRING))
            {
                var lines = connection.Update<User>(user);

                Console.WriteLine($"Usuário Atualizado");
            }
        }

        public static void DeleteUser()
        {
            using (var connection = new SqlConnection(CONNECTION_STRING))
            {
                var user = connection.Get<User>(2);
                connection.Delete<User>(user);

                Console.WriteLine($"Usuário Excluído");
            }
        }
    }
}