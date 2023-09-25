using Store.Domain.Commands.Interfaces;

namespace Store.Domain.Handlers.interfaces
{
    public interface IHandler<T> where T : ICommand
    {
        ICommandResult Handle(T command);
    }
}