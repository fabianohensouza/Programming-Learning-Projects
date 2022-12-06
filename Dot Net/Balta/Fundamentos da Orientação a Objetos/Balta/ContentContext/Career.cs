using System.Collections.Generic;

namespace Balta.ContentContext
{
    public class Career : Content
    {

        public IList<CareerItem> Items { get; set; }
        public int TotalCourses => Items.Count; //Expression Body: only one line of return and without set

        public Career(string title, string url)
            : base(title, url)
        {
            Items = new List<CareerItem>();
        }
    }
}