using Blog.Data;
using Blog.Models;
using Blog.ViewModels;
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
            try
            {
                var categories = await context.Categories.ToListAsync();
                return Ok(categories);
            }
            catch (Exception ex)
            {
                return StatusCode(500, "05XE08 - Falha interna");
            }
        }

        [HttpGet("v1/categories/{id:int}")]
        public async Task<ActionResult> GetByIdAsync(
            [FromRoute] int id,
            [FromServices] BlogDataContext context)
        {
            try
            {
                var category = await context
                                 .Categories
                                 .FirstOrDefaultAsync(x => x.Id == id);

                if (category == null)
                    return NotFound();

                return Ok(category);
            }
            catch (Exception ex)
            {
                return StatusCode(500, "05XE07 - Falha interna");
            }
            
        }

        [HttpPost("v1/categories")]
        public async Task<ActionResult> PostAsync(
            [FromBody] CreateCategoryViewModel model,
            [FromServices] BlogDataContext context)
        {
            try
            {
                var category = new Category 
                {
                    Id = 0,
                    Posts = null,
                    Name = model.Name,
                    Slug = model.Slug.ToLower(),
                };

                await context.Categories.AddAsync(category);
                await context.SaveChangesAsync();

                return Created($"v1/categories/{category.Id}", category);
            }
            catch (DbUpdateException ex)
            {
                return StatusCode(500, "05XE09 - Não foi possível incluir a categoria");
            }
            catch (Exception ex)
            {
                return StatusCode(500, "05XE10 - Falha interna");
            }
        }

        [HttpPut("v1/categories/{id:int}")]
        public async Task<ActionResult> PutAsync(
            [FromBody] CreateCategoryViewModel model,
            [FromRoute] int id,
            [FromServices] BlogDataContext context)
        {          
            try
            {
                var category = await context
                                 .Categories
                                 .FirstOrDefaultAsync(x => x.Id == id);

                if (category == null)
                    return NotFound();

                category.Name = model.Name;
                category.Slug = model.Slug;

                context.Categories.Update(category);
                await context.SaveChangesAsync();

                return Ok(category);
            }
            catch (DbUpdateException ex)
            {
                return StatusCode(500, "05XE11 - Não foi possível alterar a categoria");
            }
            catch (Exception ex)
            {
                return StatusCode(500, "05XE12 - Falha interna");
            }
        }

        [HttpDelete("v1/categories/{id:int}")]
        public async Task<ActionResult> DeleteAsync(
            [FromRoute] int id,
            [FromServices] BlogDataContext context)
        {
            try
            {
                var category = await context
                                     .Categories
                                     .FirstOrDefaultAsync(x => x.Id == id);

                if (category == null)
                    return NotFound();

                context.Categories.Remove(category);
                await context.SaveChangesAsync();

                return Ok(category);
            }
            catch (DbUpdateException ex)
            {
                return StatusCode(500, "05XE13 - Não foi possível excluir a categoria");
            }
            catch (Exception ex)
            {
                return StatusCode(500, "05XE14 - Falha interna");
            }
        }
    }
}
