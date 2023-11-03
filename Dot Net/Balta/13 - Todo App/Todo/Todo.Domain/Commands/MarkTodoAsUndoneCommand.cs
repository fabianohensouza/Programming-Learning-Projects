
using Flunt.Notifications;
using Todo.Domain.Commands.Contracts;

namespace Todo.Domain.Commands
{
    public class MarkTodoAsUndoneCommand : Notifiable, ICommand
    {
        public MarkTodoAsUndoneCommand() { }

        public MarkTodoAsUndoneCommand(Guid id, string? user)
        {
            Id = id;
            User = user;
        }

        public Guid Id { get; set; }
        public string? User { get; set; }

        public void Validate()
        {
            AddNotifications(
                new Flunt.Validations.Contract()
                    .Requires()
                    .HasMinLen(User, 3, "User", "Usuário inválido")
            );
        }
    }
}