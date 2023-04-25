using System;
using System.Linq;
using Blog.Data;
using Blog.Models;
using Microsoft.EntityFrameworkCore;

namespace Blog
{
    class Program
    {
        static void Main(string[] args)
        {
            using var context = new BlogDataContext();

            var user = new User
            {
                Name = "Fabiano Souza",
                Slug = "fabiano-souza",
                Email = "fabiano.souza@gmail.com",
                Bio = ".NET Student",
                Image = "https://image.jpg",
                PasswordHash = "12345678"
            };

            var category = new Category
            {
                Name = "BackEnd",
                Slug = "backend"
            };

            var post = new Post
            {
                Author = user,
                Category = category,
                Body = "<p>Hello World!</p>",
                Slug = "starting-with-ef-core",
                Summary = "In this article we will laern about EF Core",
                Title = "Starting With EF Core",
                CreateDate = DateTime.Now,
                LastUpdateDate = DateTime.Now
            };

            context.Posts.Add(post);
            context.SaveChanges();

        }
    }
}
