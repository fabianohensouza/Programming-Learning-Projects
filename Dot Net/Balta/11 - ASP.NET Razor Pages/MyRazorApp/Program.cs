var builder = WebApplication.CreateBuilder(args);
builder.Services.AddRazorPages(); //Adding support to Razor Pages

var app = builder.Build();

//app.MapGet("/", () => "Hello World!"); == Base Endpoint automatically created

//Configuring Razor
app.UseHttpsRedirection();
app.UseStaticFiles();  //Enabling the use of static file (eg. CCS, JS, Images)

//Mapping pages
app.UseRouting();
app.MapRazorPages();

app.Run();
