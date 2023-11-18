using System.Linq;
using Todo.Domain.Entities;
using Todo.Domain.Queries;

namespace Todo.Domain.Tests.QueriesTests
{
    [TestClass]
    public class TodoQueryTests
    {
        private List<TodoItem> _items;

        public TodoQueryTests()
        {
            _items = new List<TodoItem>();
            _items.Add(new TodoItem("Tarefa 1", "usuarioA", DateTime.Now));
            _items.Add(new TodoItem("Tarefa 2", "fabianosouza", DateTime.Now.AddDays(15)));
            _items.Add(new TodoItem("Tarefa 3", "usuarioA", DateTime.Now.AddDays(15)));
            _items.Add(new TodoItem("Tarefa 4", "fabianosouza", DateTime.Now));
            _items.Add(new TodoItem("Tarefa 5", "usuarioA", DateTime.Now.AddDays(15)));
            _items.Add(new TodoItem("Tarefa 6", "usuarioA", DateTime.Now));
            _items.Add(new TodoItem("Tarefa 7", "fabianosouza", DateTime.Now.AddDays(15)));
            _items.Add(new TodoItem("Tarefa 8", "usuarioA", DateTime.Now));

            var item = _items.FirstOrDefault(a => a.Title == "Tarefa 2");
            item?.MarkAsDone();
            item = _items.FirstOrDefault(a => a.Title == "Tarefa 3");
            item?.MarkAsDone();
            item = _items.FirstOrDefault(a => a.Title == "Tarefa 4");
            item?.MarkAsDone();
        }

        [TestMethod]
        public void ShouldReturnOnlyTheTaskOfTheUserFabianosouza()
        {
            var result = _items.AsQueryable().Where(TodoQueries.GetAll("fabianosouza"));
            Assert.AreEqual(3, result.Count());
        }

        [TestMethod]
        public void ShouldReturnAllDoneTasksOfTheUserFabianosouza()
        {
            var result = _items.AsQueryable().Where(TodoQueries.GetAllDone("fabianosouza"));
            Assert.AreEqual(2, result.Count());
        }

        [TestMethod]
        public void ShouldReturnAllUndoneTasksOfTheUserFabianosouza()
        {
            var result = _items.AsQueryable().Where(TodoQueries.GetAllUndone("fabianosouza"));
            Assert.AreEqual(1, result.Count());
        }

        [TestMethod]
        public void ShouldReturnAllDoneTasksOfTheUserFabianosouzaInAGivenDate()
        {
            var result = _items.AsQueryable().Where(TodoQueries.GetByPeriod(
                "fabianosouza",
                DateTime.Now.AddDays(15),
                true));
            Assert.AreEqual(1, result.Count());
        }
    }
}