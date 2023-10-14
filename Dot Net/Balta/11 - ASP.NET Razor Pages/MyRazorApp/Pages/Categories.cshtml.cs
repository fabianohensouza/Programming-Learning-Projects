using Microsoft.AspNetCore.Mvc.RazorPages;

namespace MyRazorApp.Pages
{
    public class Categories : PageModel
    {
        public List<Category> CategoryList { get; set; } = new();
        public int Take { get; set; } = 30;
        public int MaxData { get; set; } = 1200;
        public int ReturnSkip { get; set; }
        public int AdvanceSkip { get; set; }
        public void OnGet(
            int skip = 0,
            int take = 30)
        {
            //ReturnSkip = ((skip - take) <= 0) ? 0 : skip - take;
            if ((skip - take) > 0)
            {
                ReturnSkip = skip - take;
            }
            else
            {
                ReturnSkip = 0;
                AdvanceSkip = Take;
            }
            AdvanceSkip = ((take + skip) >= MaxData) ? MaxData : skip + take;

            var tempData = new List<Category>();

            for (int i = 0; i <= MaxData; i++)
            {
                tempData.Add(
                    new Category(i, $"Categoria-{i}", i * 5.87M)
                );
            }

            CategoryList = tempData
                .Skip(skip).
                Take(take).
                ToList();

        }

        public void OnPost()
        {
        }
    }

    public record Category(
        int Id,
        string Title,
        decimal Price);
}