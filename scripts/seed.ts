import { db } from "../server/db";
import { walkers, type InsertWalker } from "@shared/schema";

const sampleWalkers: InsertWalker[] = [
  {
    name: "Sarah M.",
    image: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
    rating: 49,
    reviewCount: 127,
    distance: "0.8 miles away",
    price: 25,
    description: "Experienced dog walker with 5+ years caring for dogs of all sizes. I love long walks in the park and ensuring your furry friend gets the exercise they need!",
    availability: "Available today",
    badges: ["Background checked"],
    backgroundCheck: true,
    insured: false,
    certified: false,
  },
  {
    name: "Mike R.",
    image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
    rating: 47,
    reviewCount: 89,
    distance: "1.2 miles away",
    price: 22,
    description: "Former veterinary technician turned professional dog walker. I specialize in high-energy dogs and provide detailed updates with photos after each walk.",
    availability: "Available tomorrow",
    badges: ["Insured"],
    backgroundCheck: false,
    insured: true,
    certified: false,
  },
  {
    name: "Emma L.",
    image: "https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
    rating: 50,
    reviewCount: 45,
    distance: "0.5 miles away",
    price: 30,
    description: "Certified dog trainer offering walking services. Perfect for dogs that need behavioral guidance or socialization during their walks. Premium service guaranteed!",
    availability: "Available today",
    badges: ["Certified trainer"],
    backgroundCheck: true,
    insured: true,
    certified: true,
  },
];

async function seed() {
  try {
    console.log("🌱 Seeding database...");
    
    // Check if walkers already exist
    const existingWalkers = await db.select().from(walkers);
    
    if (existingWalkers.length > 0) {
      console.log("✅ Database already seeded with walkers");
      return;
    }
    
    // Insert sample walkers
    await db.insert(walkers).values(sampleWalkers);
    
    console.log(`✅ Successfully seeded ${sampleWalkers.length} walkers`);
  } catch (error) {
    console.error("❌ Error seeding database:", error);
    throw error;
  }
}

seed()
  .then(() => {
    console.log("🎉 Seeding completed!");
    process.exit(0);
  })
  .catch((error) => {
    console.error("💥 Seeding failed:", error);
    process.exit(1);
  });