namespace Todo.Domain.Entities
{
    // Open-Closed Class
    //Open to extensions due to be an abstract class
    //Closed tode changed outside - Private set Id

    public abstract class Entity : IEquatable<Entity>
    {
        public Entity()
        {
            Id = Guid.NewGuid();
        }

        public Guid Id { get; private set; }

        public bool Equals(Entity? other)
        {
            if (other == null)
                return false;

            return Id == other.Id;
        }
    }
}