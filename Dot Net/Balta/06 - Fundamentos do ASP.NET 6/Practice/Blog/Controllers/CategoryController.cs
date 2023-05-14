using Blog.Data;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace Blog.Controllers
{
    [ApiController]
    public class CategoryController : ControllerBase
    {
        [HttpGet("v1/categories")]
        public async Task<ActionResult> GetAsync(
            [FromServices] BlogDataContext context)
        {
            var categories = await context.Categories.ToListAsync();
            return Ok(categories);
        }
    }
}
