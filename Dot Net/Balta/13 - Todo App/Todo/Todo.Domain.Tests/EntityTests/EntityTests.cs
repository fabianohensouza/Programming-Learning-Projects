using Todo.Domain.Entities;

namespace Todo.Domain.Tests.EntityTests
{
    [TestClass]
    public class EntityTests
    {
        private TodoItem todo = new TodoItem(
            "Titulo da tarefa",
            "fabiano.souza",
            DateTime.Now.AddMonths(1)
        );

        [TestMethod]
        public void GivenANewTodoItsDonePropShouldBeFalse()
        {
            Assert.AreEqual(todo.Done, false);
        }

        [TestMethod]
        public void ShouldMarkTodoAsDone()
        {
            todo.MarkAsDone();

            Assert.AreEqual(todo.Done, true);
        }

        [TestMethod]
        public void ShouldUpdateTodoTitle()
        {
            var newTitle = "Novo Titulo";
            todo.UpdateTitle(newTitle);

            Assert.AreEqual(todo.Title, newTitle);
        }
    }
}