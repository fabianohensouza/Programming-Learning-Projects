var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

app.MapGet("/", () => "Hello World!");
app.MapGet("/teste", () => "Teste!");

app.MapGet("/results", () =>
{
    return Results.Ok("Hello World!");
});

app.MapGet("/{nome}", (string nome) =>
{
    return Results.Ok($"Olá {nome}");
});

app.Run();
