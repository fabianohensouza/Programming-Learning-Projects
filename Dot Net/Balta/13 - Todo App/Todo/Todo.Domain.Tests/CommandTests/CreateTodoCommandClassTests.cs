using Todo.Domain.Commands;

namespace Todo.Domain.Tests.CommandTests
{
    [TestClass]
    public class CreateTodoCommandClassTests
    {
        private readonly CreateTodoCommand _invalidCommand = new CreateTodoCommand(
            "",
            "",
            DateTime.Now);
        private readonly CreateTodoCommand _validCommand = new CreateTodoCommand(
            "Tarefa teste",
            "fabiano.souza",
            DateTime.Now.AddDays(30));

        public CreateTodoCommandClassTests()
        {
            _invalidCommand.Validate();
            _validCommand.Validate();
        }

        [TestMethod]
        public void GivenAnInvalidCommandShouldFail()
        {
            Assert.AreEqual(_invalidCommand.Valid, false);
        }

        [TestMethod]
        public void GivenAValidCommandShouldSucced()
        {
            Assert.AreEqual(_validCommand.Valid, true);
        }
    }
}