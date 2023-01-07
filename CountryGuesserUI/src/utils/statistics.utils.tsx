export const getPlayerStatistics = (currentUser: any): Promise<{ wonGames: number, playedGames: number, losedGames: number }> => {
    return fetch(`https://${process.env.REACT_APP_API_URI}/player/getleaderboardstats`, {
      method: "POST",
      body: JSON.stringify({ player_id: currentUser.player_id })
    })
    .then(data => data.json())
    .then(data => {
      const { games_won, games_played } = data;

      return {
        wonGames: games_won,
        playedGames: games_played,
        losedGames: games_played - games_won,
      }
    });
}