using Todo.Domain.Commands;
using Todo.Domain.Handlers;
using Todo.Domain.Tests.Repositories;

namespace Todo.Domain.Tests.CommandTests
{
    [TestClass]
    public class CreateTodoCommandTests
    {
        private readonly CreateTodoCommand _invalidCommand = new CreateTodoCommand(
            "",
            "",
            DateTime.Now);
        private readonly CreateTodoCommand _validCommand = new CreateTodoCommand(
            "Tarefa teste",
            "fabiano.souza",
            DateTime.Now.AddDays(30));
        private readonly TodoHandler _handler = new TodoHandler(new FakeTodoRepository());

        [TestMethod]
        public void GivenAnInvalidCommandShouldStopExecution()
        {
            var result = (GenericCommandResult)_handler.Handle(_invalidCommand);

            Assert.AreEqual(result.Success, false);
        }

        [TestMethod]
        public void GivenAValidCommandShouldCreateATask()
        {
            var result = (GenericCommandResult)_handler.Handle(_validCommand);

            Assert.AreEqual(result.Success, true);
        }
    }
}