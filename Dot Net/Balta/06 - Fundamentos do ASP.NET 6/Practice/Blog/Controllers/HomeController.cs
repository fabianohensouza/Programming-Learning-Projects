using Microsoft.AspNetCore.Mvc;

namespace Blog.Controllers
{
    [ApiController]
    [Route("")]
    public class HomeController : ControllerBase
    {
        [HttpGet("")]
        public IActionResult Get() 
        {
            return Ok( new
            {
                status = "Ok!",
                version = "v1.0"
            });
        }
    }
}
