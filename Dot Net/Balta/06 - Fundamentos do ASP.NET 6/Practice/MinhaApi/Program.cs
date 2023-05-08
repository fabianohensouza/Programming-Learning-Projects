var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

app.MapGet("/", () => "Hello World!");

app.MapGet("/results", () =>
{
    return Results.Ok("Hello World!");
});

app.MapGet("/{nome}", (string nome) =>
{
    return Results.Ok($"Olá {nome}");
});

app.MapPost("/", (User user) =>
{
    return Results.Ok(user);
});

app.Run();

public class User
{
    public int Id { get; set; }
    public string Username { get; set; }
}

public class User2
{
    public int Id { get; set; }
    public string Username { get; set; }
}