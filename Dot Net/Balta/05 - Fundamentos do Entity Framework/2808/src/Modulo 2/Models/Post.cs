using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;

namespace Blog.Models
{
    [Table("Post")]
    public class Post
    {
        public int Id { get; set; }

        public string Title { get; set; }
        public string Summary { get; set; }
        public string Body { get; set; }
        public string Slug { get; set; }
        public DateTime CreateDate { get; set; }
        public DateTime LastUpdateDate { get; set; }

        public Category Category { get; set; }
        public User Author { get; set; }

        public IList<Tag> Tags { get; set; }
    }
}