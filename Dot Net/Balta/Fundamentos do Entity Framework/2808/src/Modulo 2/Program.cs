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

            //Insert
            // context.Users.Add(new User
            // {
            //     Name = "Sabrina Santos",
            //     Slug = "sabrina-santos",
            //     Email = "sabrina.santos@gmail.com",
            //     Bio = ".NET Student",
            //     Image = "https://imagesab.jpg",
            //     PasswordHash = "12345678"
            // });
            context.SaveChanges();

            var user = context.Users?
                .AsNoTracking()
                .Skip(0)
                .Take(50)
                .ToList();

            var post = new Post
            {
                Author = user,
                Category = new Category
                {
                    Name = "FrontEnd",
                    Slug = "frontend"
                },
                Body = "Meu artigo2",
                Slug = "meu-artigo2",
                Summary = "Neste segundo artigo vamos conferir",
                Title = "Meu Segundo Artigo",
                CreateDate = DateTime.Now,
            };

            context.Posts.Add(post);
            context.SaveChanges();
        }
    }
}